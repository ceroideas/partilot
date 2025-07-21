<?php
public function get_refuges(  ) {

		global $wpdb;
		$parameters = [];
		$booking_id = 9281;

		$order_table = $wpdb->prefix . 'posts'; // Supongamos que las órdenes están en la tabla 'posts'
		$user_table = $wpdb->prefix . 'users';
		
 		if (isset($_GET['id'])) {
			$product_id =  isset($_GET['id']) ? $_GET['id'] : null;
			$product = wc_get_product($product_id);

			$booking_data = get_post_meta($product_id, '_booking_data', true);
			$rules = get_post_meta($product_id, '_yith_booking_availability_range', true);
			$max_places = get_post_meta($product_id, '_yith_booking_max_per_block', true);

			global $wpdb;
		    $query = "SELECT * FROM {$wpdb->prefix}yith_wcbk_booking_meta_lookup WHERE product_id = $product_id AND status != 'bk-cancelled'";
		    $booking_ids = $wpdb->get_results($query);
			
			if ($product) {

				$parameters['fecha_solicitada'] = isset($_GET['fecha_solicitada']) ? $_GET['fecha_solicitada']  : date('Y-m-d');

				$response_dates = $this->generateDays($rules,$max_places,$booking_ids,$parameters,90);
				
				return [$product_id => ["RefName" => $product->name, "Days" => $response_dates]];
				return rest_ensure_response($rules);
			} else {
				return new WP_Error('product_not_found', 'Producto no encontrado', array('status' => 404));
			}
		} else {
			return new WP_Error('missing_parameters', 'Parámetros faltantes', array('status' => 400));
		} 
	}

	public function make_prebooking( $request ) {

		// AQUI FALTA METER LA CONDICION USANDO LA FECHA DE RESERVA, COMPROBAR QUE TODOS LOS DIAS PUEDE RESERVAR LA CANTIDAD DE PLAZAS QUE SOLICITA

		global $wpdb;
		$parameters = $request->get_json_params();
		$table_name = $wpdb->prefix . 'yith_wcbk_booking_meta_lookup';
		$url = get_bloginfo('url');

		if (!$parameters) {
			return ["response"=>["result"=>["error"=>400],"response"=>false]];
		}

		if (isset($parameters['refugeID'])) {

			$data = array(
			    array(
			        'id' => 0,
			        'title' => 'Nº de Persones',
			        'number' => $parameters['places']
			    )
			);

			$serialized_data = serialize($data);
		
			$from = $parameters['dateFrom'];
			$to = date('Y-m-d',strtotime($from.'+ '.$parameters['days'].' days'));

			$product_id = $parameters['refugeID'];
			$product = wc_get_product($product_id);
			$date = date("Y-m-d H:i:s");

			$query = "SELECT * FROM {$wpdb->prefix}yith_wcbk_booking_meta_lookup WHERE product_id = $product_id AND status != 'bk-cancelled'";

			$rules = get_post_meta($product_id, '_yith_booking_availability_range', true);
			$max_places = get_post_meta($product_id, '_yith_booking_max_per_block', true);
		    $booking_ids = $wpdb->get_results($query);

		    $parameters['fecha_solicitada'] = $from;

			$days = $this->generateDays($rules,$max_places,$booking_ids,$parameters,$parameters['days']);

			$bookable = 0;

			foreach ($days as $key => $value) {
				if ($value['places'] - $parameters['days'] < 0) {
					$bookable++;
				}
			}

			if ($bookable>0) {
				return ["response"=>["response"=>false]];
			}else{
				$data = [

					"post_author" => 1,
					"post_date" => $date,
					"post_date_gmt" => $date,
					"post_title" => $product->name,
					"post_status" => "bk-unpaid",
					"comment_status" => "closed",
					"ping_status" => "closed",
					"post_name" => sanitize_title($product->name),
					
					"post_modified" => $date,
					"post_modified_gmt" => $date,

					"post_parent" => 0,

					"menu_order" => 0,
					"post_type" => "yith_booking",
					"comment_count" => 0,
				];

				$post_id = wp_insert_post($data);

				$updated_post = array(
				    'ID'     => $post_id,
				    'guid'   => $url.'?post_type=yith_booking&p='.$post_id
				);
				wp_update_post($updated_post);

				$data = array(
				    'booking_id' => $post_id,
				    'product_id' => $product_id,
				    'user_id' => 0,
				    'status' => 'bk-unpaid',
				    'from' => $from, 
				    'to' => $to,
				    'persons' => $parameters['places']
				);

				$format = array('%d', '%d', '%d', '%s', '%s', '%s', '%d');

				$wpdb->insert($table_name, $data, $format);

				update_post_meta($post_id, '_product_id', $parameters['refugeID']);
				update_post_meta($post_id, '_title', $product->name);
				update_post_meta($post_id, '_from', strtotime($from));
				update_post_meta($post_id, '_to', strtotime($to));
				update_post_meta($post_id, '_duration', $parameters['days']);
				update_post_meta($post_id, '_duration_unit', "day");
				update_post_meta($post_id, '_order_id', "0");
				update_post_meta($post_id, '_order_item_id', "0");
				update_post_meta($post_id, '_user_id', 0);
				update_post_meta($post_id, '_can_be_cancelled', "no");
				update_post_meta($post_id, '_cancelled_duration', "7");
				update_post_meta($post_id, '_cancelled_unit', "day");
				update_post_meta($post_id, '_location',  "");
				update_post_meta($post_id, '_all_day', "no");
				update_post_meta($post_id, '_persons', $parameters['places']);
				update_post_meta($post_id, '_person_types', $serialized_data);
				update_post_meta($post_id, '_has_persons', "yes");
				update_post_meta($post_id, '_service_quantities', "a:0:{}");
				update_post_meta($post_id, '_xsalto_booking_id', $parameters['bookingID']);

				return ["response"=>["response"=>true]];
			}
		}
	}

	public function make_customer( $request )
	{
		global $wpdb;
		$params = $request->get_json_params();

		if (!$params) {
			return ["response"=>["result"=>["error"=>400],"response"=>false]];
		}

		$results = $wpdb->get_results("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_xsalto_booking_id' AND meta_value = '".$params['bookingID']."'");
		
		$post_id = $results[0]->post_id;

		if (!$post_id) {
			return ["response"=>["response"=>false]];
		}

		if (email_exists($params['customer']['email'])) {
			$user = get_user_by('email', $params['customer']['email']);
			$user_id = $user->ID;
		}else{

			$userdata = [
				"user_login" => str_replace('-', '.', sanitize_title($params['customer']['name'])),
				"user_pass" => "password",
				"user_nicename" => sanitize_title($params['customer']['name']),
				"user_email" => $params['customer']['email'],
				"user_registered" => date("Y-m-d H:i:s"),
				"user_status" => 0,
				"display_name" => $params['customer']['name'],
			];

			$user_id = wp_insert_user($userdata);

		}
		update_post_meta($post_id, '_user_id', $user_id);

		$result = $wpdb->get_results("UPDATE {$wpdb->prefix}yith_wcbk_booking_meta_lookup SET user_id = $user_id WHERE booking_id = $post_id");

		return ["response"=>["response"=>true]];
	}

	public function make_payment ($request){

	 	global $wpdb;
		$params = $request->get_json_params();

		if (!$params) {
			return ["response"=>["result"=>["error"=>400],"response"=>false]];
		}

		$post = $wpdb->get_results("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_xsalto_booking_id' AND meta_value = '".$params['bookingID']."'");
		$post_name = $wpdb->get_results("SELECT post_title FROM {$wpdb->prefix}postmeta WHERE meta_key = '_xsalto_booking_id' AND meta_value = '".$params['bookingID']."'");

		$post_id = $post[0]->post_id;
		$user = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}yith_wcbk_booking_meta_lookup WHERE booking_id = $post_id");
		$user_id = $user[0]->user_id;
		$user_info = get_user_by('ID', $user_id);

		if (!$post_id) {
			return ["response"=>["response"=>true]];
		}
		$updated_post_principal = array(
		   'ID'     => $post_id,
		   'post_status'   => 'bk-paid'
	    );
   
	    wp_update_post($updated_post_principal);
	    $date = date("Y-m-d H:i:s");
	    $timestamp = strtotime($date);
	    $name = explode(" ", $user_info->data->display_name);
	    $product = wc_get_product($product_id);
	    $user_display = get_user_by('display_name', $product->name);
	    $user_display_id = $user_display->ID;
   
	    $data = [
   
		   "post_author" => 1,
		   "post_date" => $date,
		   "post_date_gmt" => $date,
		   "post_title" => 'Order &ndash; ' . date('F j, Y @ h:i A', $timestamp),
		   "post_status" => "wc-completed",
		   "comment_status" => "closed",
		   "ping_status" => "closed",
		   "post_name" => sanitize_title('Order &ndash; ' . date('F j, Y @ h:i A', $timestamp)),
		   
		   "post_modified" => $date,
		   "post_modified_gmt" => $date,
   
		   "post_parent" => 0,
   
		   "menu_order" => 0,
		   "post_type" => "shop_order",
		   "comment_count" => 0,
	    ];
   
	    $post_id_shop_1 = wp_insert_post($data);
   
   
	    $post_id_shop_2 = wp_insert_post($data);
   
   
   
		$updated_post_shop_1 = array(
		   'ID'     => $post_id_shop_1,
		   'guid'   => $url.'?post_type=yith_booking&p='.$post_id_shop_1,
		   
	    );
   
	    wp_update_post($updated_post_shop_1); 
   
	    $updated_post_shop_2 = array(
		   'ID'     => $post_id_shop_2,
		   'guid'   => $url.'?post_type=yith_booking&p='.$post_id_shop_2,
		   'post_parent' => $post_id_shop_1
		   
	    );


		$wpdb->show_errors();
		$wpdb->insert('wpol_woocommerce_order_items', array(
		'order_item_name' => $product->name || 'error',
		'order_item_type' => 'line_item',
		'order_id'        => $post_id_shop_2,
		), array('%s', '%s', '%d'));
		   if ($wpdb->last_error) {
			   echo $wpdb->last_error;
		}
		$wpdb->hide_errors();

		$new_order_id = $wpdb->insert_id;

		$wpdb->insert(
		   'wpol_wc_order_stats',
		   array(
			   'order_id'=> $post_id_shop_2,
			   'parent_id' => 0,
			   'date_created' => $date,
			   'date_created_gmt' => $date,
			   'num_items_sold' => 1,
			   'total_sales'=> $params['payment']['duplication']['currentAmount'],
			   'status'=>'wc-completed',
			   'customer_id'=> $user_id,
			   'returning_customer'=> 0

		   ),
		   
		); 
		if ($wpdb->last_error) {
		   echo $wpdb->last_error;
   		}
   
	    wp_update_post($updated_post_shop_2); 
	    $wpdb->get_results("UPDATE {$wpdb->prefix}yith_wcbk_booking_meta_lookup SET order_id = $post_id_shop_2  WHERE booking_id = $post_id"); 
		$wpdb->get_results("UPDATE {$wpdb->prefix}yith_wcbk_booking_meta_lookup SET status = 'bk-paid' WHERE booking_id = $post_id"); 
		$wpdb->get_results("UPDATE {$wpdb->prefix}postmeta SET meta_value = $post_id_shop_2 WHERE post_id = $post_id AND meta_key = '_order_id' "); 
		$wpdb->get_results("UPDATE {$wpdb->prefix}postmeta SET meta_value = $new_order_id  WHERE post_id = $post_id AND meta_key = '_order_item_id' "); 
   
		wc_add_order_item_meta($new_order_id, '_deposit_value', $params['payment']['duplication']['currentAmount'] );
		wc_add_order_item_meta($new_order_id, '_deposit', 1);
	

		update_post_meta($post_id_shop_2, '_order_key', 'wc_order_'.uniqid());
		update_post_meta($post_id_shop_2, '_customer_user', $user_id);
		update_post_meta($post_id_shop_2, '_payment_method', 'xsalto');
		update_post_meta($post_id_shop_2, '_payment_method_title', 'xSalto');
		update_post_meta($post_id_shop_2, '_customer_ip_address', 0);
		update_post_meta($post_id_shop_2, '_customer_user_agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36 Edg/121.0.0.0');
		update_post_meta($post_id_shop_2, '_created_via', "yith_wcmv_vendor_suborder");
		update_post_meta($post_id_shop_2, '_download_permissions_granted', "yes");
		update_post_meta($post_id_shop_2, '_recorded_sales', 'yes');
		update_post_meta($post_id_shop_2, '_recorded_coupon_usage_counts', "no");
		update_post_meta($post_id_shop_2, '_new_order_email_sent', "no");
		update_post_meta($post_id_shop_2, '_order_stock_reduced', "yes");
		update_post_meta($post_id_shop_2, '_billing_first_name',  $name[0]);
		update_post_meta($post_id_shop_2, '_billing_last_name', implode(" ", array_slice($name, 1)));
		update_post_meta($post_id_shop_2, '_billing_address_1', '');
		update_post_meta($post_id_shop_2, '_billing_city', '');
		update_post_meta($post_id_shop_2, '_billing_state', "");
		update_post_meta($post_id_shop_2, '_billing_postcode', "");
		update_post_meta($post_id_shop_2, '_billing_country', "ES"); 
		update_post_meta($post_id_shop_2, '_billing_email', $user_info->data->user_email);
		update_post_meta($post_id_shop_2, '_billing_phone', 0); 
		update_post_meta($post_id_shop_2, '_order_currency', "EUR");
		update_post_meta($post_id_shop_2, '_cart_discount', 0); 
		update_post_meta($post_id_shop_2, '_cart_discount_tax', "0");
		update_post_meta($post_id_shop_2, '_order_shipping', 0); 
		update_post_meta($post_id_shop_2, '_order_shipping_tax', 0); 
		update_post_meta($post_id_shop_2, '_order_tax', 0); 
		update_post_meta($post_id_shop_2, '_order_total', $params['duplication']['currentAmount']); 
		update_post_meta($post_id_shop_2, '_order_version', "8.0.0"); 
		update_post_meta($post_id_shop_2, '_prices_include_tax', "no"); 
		update_post_meta($post_id_shop_2, '_billing_address_index', ""); 
		update_post_meta($post_id_shop_2, '_shipping_address_index',""); 
		update_post_meta($post_id_shop_2, 'vendor_id', $user_display_id); 
		update_post_meta($post_id_shop_2, '_billing_Dni_·_Nie_·_Passaport', 0); 
		update_post_meta($post_id_shop_2, 'is_vat_exempt', "no"); 
		update_post_meta($post_id_shop_2, 'additional_Federació', ""); 
		update_post_meta($post_id_shop_2, 'additional_sortidatravessa', ''); 
		update_post_meta($post_id_shop_2, '_ywson_custom_number_order_complet', $params['payment']['duplication']['bk_transaction_id']); 
		update_post_meta($post_id_shop_2, '_ywson_subnumber_created', "no"); 
		update_post_meta($post_id_shop_2, '_commissions_processed', 'no'); 
		update_post_meta($post_id_shop_2, 'yith_bookings', 'a:1:{i:0;i:'.$post_id.';}'); 
		update_post_meta($post_id_shop_2, '_date_completed', 0); 
		update_post_meta($post_id_shop_2, '_date_paid', 0); 
		update_post_meta($post_id_shop_2, '_paid_date', $date); 
		update_post_meta($post_id_shop_2, '_completed_date', $date);   

		update_post_meta($post_id_shop_1, '_order_key', 'wc_order_'.uniqid());
		update_post_meta($post_id_shop_1, '_customer_user', $user_id);
		update_post_meta($post_id_shop_1, '_payment_method', 'xsalto');
		update_post_meta($post_id_shop_1, '_payment_method_title', 'xSalto');
		update_post_meta($post_id_shop_1, '_customer_ip_address', 0);
		update_post_meta($post_id_shop_1, '_customer_user_agent', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/121.0.0.0 Safari/537.36 Edg/121.0.0.0');
		update_post_meta($post_id_shop_1, '_created_via', "yith_wcmv_vendor_suborder");
		update_post_meta($post_id_shop_1, '_download_permissions_granted', "yes");
		update_post_meta($post_id_shop_1, '_recorded_sales', 'yes');
		update_post_meta($post_id_shop_1, '_recorded_coupon_usage_counts', "no");
		update_post_meta($post_id_shop_1, '_new_order_email_sent', "no");
		update_post_meta($post_id_shop_1, '_order_stock_reduced', "yes");
		update_post_meta($post_id_shop_1, '_billing_first_name',  $name[0]);
		update_post_meta($post_id_shop_1, '_billing_last_name', implode(" ", array_slice($name, 1)));
		update_post_meta($post_id_shop_1, '_billing_address_1', '');
		update_post_meta($post_id_shop_1, '_billing_city', '');
		update_post_meta($post_id_shop_1, '_billing_state', "");
		update_post_meta($post_id_shop_1, '_billing_postcode', "");
		update_post_meta($post_id_shop_1, '_billing_country', "ES"); 
		update_post_meta($post_id_shop_1, '_billing_email', $user_info->data->user_email);
		update_post_meta($post_id_shop_1, '_billing_phone', 0); 
		update_post_meta($post_id_shop_1, '_order_currency', "EUR");
		update_post_meta($post_id_shop_1, '_cart_discount', 0); 
		update_post_meta($post_id_shop_1, '_cart_discount_tax', "0");
		update_post_meta($post_id_shop_1, '_order_shipping', 0); 
		update_post_meta($post_id_shop_1, '_order_shipping_tax', 0); 
		update_post_meta($post_id_shop_1, '_order_tax', 0); 
		update_post_meta($post_id_shop_1, '_order_total', $params['payment']['duplication']['currentAmount']); 
		update_post_meta($post_id_shop_1, '_order_version', "8.0.0"); 
		update_post_meta($post_id_shop_1, '_prices_include_tax', "no"); 
		update_post_meta($post_id_shop_1, '_billing_address_index', ""); 
		update_post_meta($post_id_shop_1, '_shipping_address_index',""); 
		update_post_meta($post_id_shop_1, 'vendor_id', $user_display_id); 
		update_post_meta($post_id_shop_1, '_billing_Dni_·_Nie_·_Passaport', 0); 
		update_post_meta($post_id_shop_1, 'is_vat_exempt', "no"); 
		update_post_meta($post_id_shop_1, 'additional_Federació', ""); 
		update_post_meta($post_id_shop_1, 'additional_sortidatravessa', ''); 
		update_post_meta($post_id_shop_1, '_ywson_custom_number_order_complet', $params['payment']['duplication']['bk_transaction_id']); 
		update_post_meta($post_id_shop_1, '_ywson_subnumber_created', "no"); 
		update_post_meta($post_id_shop_1, '_commissions_processed', 'no'); 
		update_post_meta($post_id_shop_1, 'yith_bookings', 'a:1:{i:0;i:'.$post_id.';}'); 
		update_post_meta($post_id_shop_1, '_date_completed', 0); 
		update_post_meta($post_id_shop_1, '_date_paid', 0); 
		update_post_meta($post_id_shop_1, '_paid_date', $date); 
		update_post_meta($post_id_shop_1, '_completed_date', $date);
		update_post_meta($post_id_shop_1, '_has_deposit', 1);
		update_post_meta($post_id_shop_1, '_wc_order_attribution_device_type', 'xsalto');  


		return ["response"=>["response"=>true]];
	   
	}

	public function make_services( $request )
	{
		global $wpdb;
		$params = $request->get_json_params();

		$results = $wpdb->get_results("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_xsalto_booking_id' AND meta_value = '".$params['bookingID']."'");	
		$post_id = $results[0]->post_id;

		if (array_keys($params['services']) === range(0, count($params['services']) - 1)) {
			$servicios = $params['services'];
		}else{
			$servicios = [$params['services']];
		}

		if ($post_id) {

			$terminos = [];

			foreach ($servicios as $key => $servicio) {

				if (array_keys($servicio['products']) === range(0, count($servicio['products']) - 1)) {
					$productos = $servicio['products'];
				}else{
					$productos = [$servicio['products']];
				}

				foreach ($productos as $key => $pr) {
					
					$termino = get_term_by('name', $pr['title'], 'yith_booking_service');

					if (!$termino) {
						$nuevo_termino = wp_insert_term($pr['title'], 'yith_booking_service');
				   		$id_termino = $nuevo_termino['term_id'];	
					}else{
						$id_termino = $termino->term_id;
					}
				   	
					update_term_meta($id_termino, "yith_shop_vendor", "243");
					update_term_meta($id_termino, "price", "0");
					update_term_meta($id_termino, "optional", "no");
					update_term_meta($id_termino, "hidden", "no");
					update_term_meta($id_termino, "hidden_in_search_forms", "no");
					update_term_meta($id_termino, "multiply_per_blocks", "no");
					update_term_meta($id_termino, "multiply_per_persons", "no");
					update_term_meta($id_termino, "price_for_person_types", 'a:1:{i:30748;s:0:"";}');
					update_term_meta($id_termino, "quantity_enabled", "no");
					update_term_meta($id_termino, "min_quantity", "0");
					update_term_meta($id_termino, "max_quantity", "0");

					$terminos[] = $id_termino;
				}

			}

			if (count($terminos)) {
		   		wp_set_post_terms($post_id, $terminos, 'yith_booking_service');

		   		return ["response"=>["response"=>true]];
			}else{
				return ["response"=>["result"=>[],"response"=>false]];
			}
		}
    }