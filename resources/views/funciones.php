<?php
// =========================
// FUNCIONES PERSONALIZADAS MOVIDAS DE funciones.php
// =========================

function generateDays($rules,$max_places,$booking_ids,$parameters,$days)
{
    $dates = [];
    $response_dates = [];
    $booking_dates = [];

    foreach ($rules as $key => $value) {

        $date_ranges = $value['date_ranges'];
        $availabilities = $value['availabilities'];

        foreach ($date_ranges as $key => $dt) {
            
            $start = $dt['from'];

            while ($start <= $dt['to']) {

                $status = 0;

                if ($availabilities[0]['day'] == 'all') {
                    $status = 1;
                }else{
                    foreach ($availabilities as $key => $av) {

                        $day_of_week = date("w",strtotime($start));
                        
                        if ($av['day'] == $day_of_week) {
                            $status = 1;
                        }

                    }
                }

                $dates[$start] = ['status'=>$status,'max_places'=> $status == 0 ? 0 : $max_places];

                $start = date("Y-m-d",strtotime($start."+ 1 days"));
            }
        }
    }

    foreach ($booking_ids as $key => $value) {

        $start = substr($value->from, 0, 10);
        $to = substr($value->to, 0, 10);
        while ($start < $to) {

            $booking_dates[$start] = $value->persons;
            $start = date("Y-m-d",strtotime($start."+ 1 days"));

        }
    }

    $fecha = isset($parameters['fecha_solicitada']) ? $parameters['fecha_solicitada'] : date('Y-m-d');
    $fecha_90 = date("Y-m-d",strtotime($fecha."+ ".$days." days"));

    while ($fecha < $fecha_90) {

        if (isset($dates[$fecha])) {

            $data = ['date' => $fecha, 'status' => $dates[$fecha]['status'], 'places' => $dates[$fecha]['max_places'], 'totalplaces' => $dates[$fecha]['max_places']];

            if (isset($booking_dates[$fecha])) {

                $data['places'] = strval($dates[$fecha]['max_places']-$booking_dates[$fecha]);
                $data['totalplaces'] = $dates[$fecha]['max_places'];
            }
        }else{
            $data = ['date' => $fecha, 'status' => 0, 'places' => 0, 'totalplaces' => 0];
        }

        $response_dates[] = $data;

        $fecha = date("Y-m-d",strtotime($fecha."+ 1 days"));
    }
    return $response_dates;
}

function get_refuges($request) {
    global $wpdb;
    $parameters = [];
    $booking_id = 9281;

    $order_table = $wpdb->prefix . 'posts';
    $user_table = $wpdb->prefix . 'users';

    if (isset($_GET['id'])) {
        $product_id = isset($_GET['id']) ? $_GET['id'] : null;
        $product = wc_get_product($product_id);

        $booking_data = get_post_meta($product_id, '_booking_data', true);
        $rules = get_post_meta($product_id, '_yith_booking_availability_range', true);
        $max_places = get_post_meta($product_id, '_yith_booking_max_per_block', true);

        $query = "SELECT * FROM {$wpdb->prefix}yith_wcbk_booking_meta_lookup WHERE product_id = $product_id AND status != 'bk-cancelled'";
        $booking_ids = $wpdb->get_results($query);

        if ($product) {
            $parameters['fecha_solicitada'] = isset($_GET['fecha_solicitada']) ? $_GET['fecha_solicitada']  : date('Y-m-d');
            $response_dates = generateDays($rules, $max_places, $booking_ids, $parameters, 90);
            return [$product_id => ["RefName" => $product->name, "Days" => $response_dates]];
        } else {
            return new WP_Error('product_not_found', 'Producto no encontrado', array('status' => 404));
        }
    } else {
        return new WP_Error('missing_parameters', 'Parámetros faltantes', array('status' => 400));
    }
}

function make_prebooking($request) {
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
                'number' => strval($parameters['places'])
            )
        );
        $serialized_data = $data;
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
        $days = generateDays($rules, $max_places, $booking_ids, $parameters, $parameters['days']);
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

function make_customer($request) {
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
    $first_name = isset($params['customer']['firstName']) ? $params['customer']['firstName'] : '';
    $full_name = isset($params['customer']['name']) ? $params['customer']['name'] : '';
    $last_name = '';
    if ($first_name && $full_name) {
        $last_name = trim(preg_replace('/^'.preg_quote($first_name, '/').'/i', '', $full_name));
    }
    if (email_exists($params['customer']['email'])) {
        $user = get_user_by('email', $params['customer']['email']);
        $user_id = $user->ID;
    }else{
        $userdata = [
            "user_login" => str_replace('-', '.', sanitize_title($full_name)),
            "user_pass" => "password",
            "user_nicename" => sanitize_title($full_name),
            "user_email" => $params['customer']['email'],
            "user_registered" => date("Y-m-d H:i:s"),
            "user_status" => 0,
            "display_name" => $full_name,
        ];
        $user_id = wp_insert_user($userdata);
    }
    // Guardar nombre y apellido como metadatos del usuario
    if ($first_name) {
        update_user_meta($user_id, 'first_name', $first_name);
        update_user_meta($user_id, 'billing_first_name', $first_name);
    }
    if ($last_name) {
        update_user_meta($user_id, 'last_name', $last_name);
        update_user_meta($user_id, 'billing_last_name', $last_name);
    }
    // Guardar nombre y apellido como metadatos de la reserva
    if ($first_name) {
        update_post_meta($post_id, '_billing_first_name', $first_name);
    }
    if ($last_name) {
        update_post_meta($post_id, '_billing_last_name', $last_name);
    }
    // Guardar el teléfono como metadato del usuario
    if (!empty($params['customer']['phone'])) {
        update_user_meta($user_id, 'billing_phone', $params['customer']['phone']);
    }
    // Guardar el teléfono como metadato de la reserva
    if (!empty($params['customer']['phone'])) {
        update_post_meta($post_id, '_billing_phone', $params['customer']['phone']);
    }
    update_post_meta($post_id, '_user_id', $user_id);
    $result = $wpdb->get_results("UPDATE {$wpdb->prefix}yith_wcbk_booking_meta_lookup SET user_id = $user_id WHERE booking_id = $post_id");
    return ["response"=>["response"=>true]];
}

function make_payment($request){
    global $wpdb;
    $params = $request->get_json_params();
    $url = get_bloginfo('url');

    if (!$params) {
        return ["response"=>["result"=>["error"=>400],"response"=>false]];
    }

    $post = $wpdb->get_results("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = '_xsalto_booking_id' AND meta_value = '".$params['bookingID']."'");
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
    $product = wc_get_product($params['refugeID'] ?? 0);
    $user_display = get_user_by('display_name', $product ? $product->name : '');
    $user_display_id = $user_display ? $user_display->ID : 0;
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
    wp_update_post($updated_post_shop_2); 
    $wpdb->show_errors();
    $wpdb->insert('wpol_woocommerce_order_items', array(
        'order_item_name' => $product ? $product->name : 'error',
        'order_item_type' => 'line_item',
        'order_id'        => $post_id_shop_2,
    ), array('%s', '%s', '%d'));
    $new_order_id = $wpdb->insert_id;
    $wpdb->insert(
       'wpol_wc_order_stats',
       array(
           'order_id'=> $post_id_shop_2,
           'parent_id' => 0,
           'date_created' => $date,
           'date_created_gmt' => $date,
           'num_items_sold' => 1,
           'total_sales'=> $params['payment']['duplication']['currentAmount'] ?? 0,
           'status'=>'wc-completed',
           'customer_id'=> $user_id,
           'returning_customer'=> 0
       )
    ); 
    $wpdb->hide_errors();
    $wpdb->get_results("UPDATE {$wpdb->prefix}yith_wcbk_booking_meta_lookup SET order_id = $post_id_shop_2  WHERE booking_id = $post_id"); 
    $wpdb->get_results("UPDATE {$wpdb->prefix}yith_wcbk_booking_meta_lookup SET status = 'bk-paid' WHERE booking_id = $post_id"); 
    $wpdb->get_results("UPDATE {$wpdb->prefix}postmeta SET meta_value = $post_id_shop_2 WHERE post_id = $post_id AND meta_key = '_order_id' "); 
    $wpdb->get_results("UPDATE {$wpdb->prefix}postmeta SET meta_value = $new_order_id  WHERE post_id = $post_id AND meta_key = '_order_item_id' "); 
    wc_add_order_item_meta($new_order_id, '_deposit_value', $params['payment']['duplication']['currentAmount'] ?? 0 );
    wc_add_order_item_meta($new_order_id, '_deposit', 1);
    update_post_meta($post_id_shop_2, '_order_key', 'wc_order_'.uniqid());
    update_post_meta($post_id_shop_2, '_customer_user', $user_id);
    update_post_meta($post_id_shop_2, '_payment_method', 'xsalto');
    update_post_meta($post_id_shop_2, '_payment_method_title', 'xSalto');
    update_post_meta($post_id_shop_2, '_customer_ip_address', 0);
    update_post_meta($post_id_shop_2, '_customer_user_agent', 'Mozilla/5.0');
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
    update_post_meta($post_id_shop_2, '_billing_phone', get_user_meta($user_id, 'billing_phone', true)); 
    
    // Actualizar metadatos de la orden padre (necesario para el calendario)
    update_post_meta($post_id_shop_1, '_billing_first_name', $name[0]);
    update_post_meta($post_id_shop_1, '_billing_last_name', implode(" ", array_slice($name, 1)));
    update_post_meta($post_id_shop_1, '_billing_email', $user_info->data->user_email);
    update_post_meta($post_id_shop_1, '_billing_phone', get_user_meta($user_id, 'billing_phone', true));
    
    // Actualizar metadatos de la reserva principal para que aparezcan en el calendario
    update_post_meta($post_id, '_billing_first_name', $name[0]);
    update_post_meta($post_id, '_billing_last_name', implode(" ", array_slice($name, 1)));
    update_post_meta($post_id, '_billing_email', $user_info->data->user_email);
    update_post_meta($post_id, '_billing_phone', get_user_meta($user_id, 'billing_phone', true));
    
    update_post_meta($post_id_shop_2, '_order_currency', "EUR");
    update_post_meta($post_id_shop_2, '_cart_discount', 0); 
    update_post_meta($post_id_shop_2, '_cart_discount_tax', "0");
    update_post_meta($post_id_shop_2, '_order_shipping', 0); 
    update_post_meta($post_id_shop_2, '_order_shipping_tax', 0); 
    update_post_meta($post_id_shop_2, '_order_tax', 0); 
    update_post_meta($post_id_shop_2, '_order_total', $params['payment']['duplication']['currentAmount'] ?? 0); 
    update_post_meta($post_id_shop_2, '_order_version', "8.0.0"); 
    update_post_meta($post_id_shop_2, '_prices_include_tax', "no"); 
    update_post_meta($post_id_shop_2, '_billing_address_index', ""); 
    update_post_meta($post_id_shop_2, '_shipping_address_index',""); 
    update_post_meta($post_id_shop_2, 'vendor_id', $user_display_id); 
    update_post_meta($post_id_shop_2, '_billing_Dni_·_Nie_·_Passaport', 0); 
    update_post_meta($post_id_shop_2, 'is_vat_exempt', "no"); 
    update_post_meta($post_id_shop_2, 'additional_Federació', ""); 
    update_post_meta($post_id_shop_2, 'additional_sortidatravessa', ''); 
    update_post_meta($post_id_shop_2, '_ywson_custom_number_order_complet', $params['payment']['duplication']['bk_transaction_id'] ?? ''); 
    update_post_meta($post_id_shop_2, '_ywson_subnumber_created', "no"); 
    update_post_meta($post_id_shop_2, '_commissions_processed', 'no'); 
    update_post_meta($post_id_shop_2, 'yith_bookings', 'a:1:{i:0;i:'.$post_id.';}'); 
    update_post_meta($post_id_shop_2, '_date_completed', 0); 
    update_post_meta($post_id_shop_2, '_date_paid', 0); 
    update_post_meta($post_id_shop_2, '_paid_date', $date); 
    update_post_meta($post_id_shop_2, '_completed_date', $date);
    update_post_meta($post_id_shop_2, '_has_deposit', 1);
    update_post_meta($post_id_shop_2, '_wc_order_attribution_device_type', 'xsalto');  
    return ["response"=>["response"=>true]];
}

function make_services($request)
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

function update_booking($request)
{
    global $wpdb;
    $params = $request->get_json_params();
    // 1. Buscar el booking por bookingID
    $results = $wpdb->get_results($wpdb->prepare("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = %s AND meta_value = %s", '_xsalto_booking_id', $params['bookingID']));
    if (empty($results) || empty($results[0]->post_id)) {
        return new WP_Error('not_found', 'No se encontró la reserva con ese bookingID', array('status' => 404));
    }
    $post_id = $results[0]->post_id;
    // 2. Obtener qty del primer producto del primer día
    if (!isset($params['days'][0]['products'][0]['qty'])) {
        return new WP_Error('invalid_request', 'No se encontró qty en el primer producto', array('status' => 400));
    }
    $qty = intval($params['days'][0]['products'][0]['qty']);
    // 3. Actualizar persons en la tabla de reservas
    $wpdb->update(
        $wpdb->prefix . 'yith_wcbk_booking_meta_lookup',
        array('persons' => $qty),
        array('booking_id' => $post_id)
    );
    // 4. Actualizar metadato _persons en postmeta
    update_post_meta($post_id, '_persons', $qty);
    // 5. Actualizar los servicios asociados (igual que make_services)
    $servicios = [];
    foreach ($params['days'] as $day) {
        if (isset($day['products']) && is_array($day['products'])) {
            foreach ($day['products'] as $pr) {
                $servicios[] = $pr;
            }
        }
    }
    $terminos = [];
    foreach ($servicios as $servicio) {
        $titulo = $servicio['title'];
        $termino = get_term_by('name', $titulo, 'yith_booking_service');
        if (!$termino) {
            $nuevo_termino = wp_insert_term($titulo, 'yith_booking_service');
            $id_termino = $nuevo_termino['term_id'];
        } else {
            $id_termino = $termino->term_id;
        }
        // Puedes personalizar los metadatos del servicio aquí si lo necesitas
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
    if (count($terminos)) {
        wp_set_post_terms($post_id, $terminos, 'yith_booking_service');
        return ["response" => ["response" => true]];
    } else {
        return ["response" => ["result" => [], "response" => false]];
    }
}

// =========================
// ENDPOINTS REST PERSONALIZADOS SIMPLES
// =========================
add_action('rest_api_init', function () {
    // GET /wp-json/wp/v2/getAvailabilities
    register_rest_route('wp/v2', '/getAvailabilities', array(
        array(
            'methods'  => WP_REST_Server::READABLE,
            'callback' => 'get_refuges',
            'permission_callback' => '__return_true',
        )
    ));
    // POST /wp-json/wp/v2/preBooking
    register_rest_route('wp/v2', '/preBooking', array(
        array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => 'make_prebooking',
            'permission_callback' => '__return_true',
        )
    ));
    // POST /wp-json/wp/v2/setCustomer
    register_rest_route('wp/v2', '/setCustomer', array(
        array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => 'make_customer',
            'permission_callback' => '__return_true',
        )
    ));
    // POST /wp-json/wp/v2/setPayment
    register_rest_route('wp/v2', '/setPayment', array(
        array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => 'make_payment',
            'permission_callback' => '__return_true',
        )
    ));
    // POST /wp-json/wp/v2/setServices
    register_rest_route('wp/v2', '/setServices', array(
        array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => 'make_services',
            'permission_callback' => '__return_true',
        )
    ));
    // POST /wp-json/wp/v2/updateBooking
    register_rest_route('wp/v2', '/updateBooking', array(
        array(
            'methods'  => WP_REST_Server::CREATABLE,
            'callback' => 'update_booking',
            'permission_callback' => '__return_true',
        )
    ));
    // DELETE /wp-json/wp/v2/deleteBooking
    register_rest_route('wp/v2', '/deleteBooking', array(
        array(
            'methods'  => WP_REST_Server::DELETABLE,
            'callback' => 'delete_booking',
            'permission_callback' => '__return_true',
        )
    ));
});

function delete_booking($request) {
    global $wpdb;
    // Obtener el bookingID desde el body (JSON) o query param
    $params = $request->get_json_params();
    $xsalto_booking_id = $params['bookingID'] ?? $request->get_param('bookingID');
    if (!$xsalto_booking_id) {
        return new WP_Error('missing_booking_id', 'Falta el parámetro bookingID', array('status' => 400));
    }
    // Buscar el post_id de la reserva usando _xsalto_booking_id
    $results = $wpdb->get_results($wpdb->prepare("SELECT post_id FROM {$wpdb->prefix}postmeta WHERE meta_key = %s AND meta_value = %s", '_xsalto_booking_id', $xsalto_booking_id));
    if (empty($results) || empty($results[0]->post_id)) {
        return new WP_Error('not_found', 'No se encontró la reserva con ese bookingID', array('status' => 404));
    }
    $booking_id = $results[0]->post_id;

    // 1. Eliminar servicios asociados (términos y relaciones)
    wp_delete_object_term_relationships($booking_id, 'yith_booking_service');

    // 2. Buscar y eliminar órdenes relacionadas (shop_order)
    $order_id = get_post_meta($booking_id, '_order_id', true);
    if ($order_id) {
        // Eliminar metadatos de la orden
        $wpdb->delete($wpdb->prefix . 'postmeta', array('post_id' => $order_id));
        // Eliminar la orden
        wp_delete_post($order_id, true);
        // Buscar subórdenes (si existen)
        $suborders = $wpdb->get_col($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE post_parent = %d AND post_type = 'shop_order'", $order_id));
        foreach ($suborders as $suborder_id) {
            $wpdb->delete($wpdb->prefix . 'postmeta', array('post_id' => $suborder_id));
            wp_delete_post($suborder_id, true);
        }
    }

    // 3. Eliminar post principal (reserva)
    $deleted = wp_delete_post($booking_id, true);
    // 4. Eliminar metadatos
    $wpdb->delete($wpdb->prefix . 'postmeta', array('post_id' => $booking_id));
    // 5. Eliminar de la tabla de reservas
    $wpdb->delete($wpdb->prefix . 'yith_wcbk_booking_meta_lookup', array('booking_id' => $booking_id));

    if ($deleted) {
        return ['response' => true, 'message' => 'Reserva, servicios y órdenes eliminados correctamente'];
    } else {
        return new WP_Error('delete_failed', 'No se pudo eliminar la reserva', array('status' => 500));
    }
}

/*{
  "bookingID": "202208011230_FRGL2VGD6EX-0",
  "days": [
    {
      "date": "2025-07-30",
      "products": [
        {
          "title": "Todos - Cena",
          "qty": 2
        },
        {
          "title": "PERNOCTA + desayuno",
          "qty": 2
        }
      ]
    }
  ]
}*/