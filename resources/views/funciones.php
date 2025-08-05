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

            $booking_dates[$start] += $value->persons;
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
        // Actualizar el display_name si es diferente
        if ($user->display_name !== $full_name) {
            wp_update_user(array(
                'ID' => $user_id,
                'display_name' => $full_name,
                "user_login" => str_replace('-', '.', sanitize_title($full_name)),
                "user_nicename" => sanitize_title($full_name),
            ));
        }
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
    
    // Guardar todos los datos del cliente como metadatos del usuario
    if ($first_name) {
        update_user_meta($user_id, 'first_name', $first_name);
        update_user_meta($user_id, 'billing_first_name', $first_name);
    }
    if ($last_name) {
        update_user_meta($user_id, 'last_name', $last_name);
        update_user_meta($user_id, 'billing_last_name', $last_name);
    }
    
    // Guardar dirección y datos adicionales
    if (!empty($params['customer']['address'])) {
        update_user_meta($user_id, 'billing_address_1', $params['customer']['address']);
    }
    if (!empty($params['customer']['city'])) {
        update_user_meta($user_id, 'billing_city', $params['customer']['city']);
    }
    if (!empty($params['customer']['postalCode'])) {
        update_user_meta($user_id, 'billing_postcode', $params['customer']['postalCode']);
    }
    if (!empty($params['customer']['country'])) {
        update_user_meta($user_id, 'billing_country', $params['customer']['country']);
    }
    if (!empty($params['customer']['phone'])) {
        update_user_meta($user_id, 'billing_phone', $params['customer']['phone']);
    }
    if (!empty($params['customer']['email'])) {
        update_user_meta($user_id, 'billing_email', $params['customer']['email']);
    }
    
    // Guardar datos adicionales como customerID y userLang
    if (!empty($params['customer']['customerID'])) {
        update_user_meta($user_id, 'customer_id', $params['customer']['customerID']);
    }
    if (!empty($params['customer']['userLang'])) {
        update_user_meta($user_id, 'user_language', $params['customer']['userLang']);
    }
    
    // Guardar todos los datos como metadatos de la reserva también
    if ($first_name) {
        update_post_meta($post_id, '_billing_first_name', $first_name);
    }
    if ($last_name) {
        update_post_meta($post_id, '_billing_last_name', $last_name);
    }
    if (!empty($params['customer']['address'])) {
        update_post_meta($post_id, '_billing_address_1', $params['customer']['address']);
    }
    if (!empty($params['customer']['city'])) {
        update_post_meta($post_id, '_billing_city', $params['customer']['city']);
    }
    if (!empty($params['customer']['postalCode'])) {
        update_post_meta($post_id, '_billing_postcode', $params['customer']['postalCode']);
    }
    if (!empty($params['customer']['country'])) {
        update_post_meta($post_id, '_billing_country', $params['customer']['country']);
    }
    if (!empty($params['customer']['phone'])) {
        update_post_meta($post_id, '_billing_phone', $params['customer']['phone']);
    }
    if (!empty($params['customer']['email'])) {
        update_post_meta($post_id, '_billing_email', $params['customer']['email']);
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
    
    // Obtener el product_id (refugeID) desde la reserva
    $product_id = get_post_meta($post_id, '_product_id', true);
    if (!$product_id) {
        // Si no está en postmeta, intentar obtenerlo desde la tabla de reservas
        $product_id = $user[0]->product_id ?? 0;
    }
    
    $updated_post_principal = array(
       'ID'     => $post_id,
       'post_status'   => 'bk-paid'
    );
    wp_update_post($updated_post_principal);
    $date = date("Y-m-d H:i:s");
    $timestamp = strtotime($date);
    
    // Obtener los datos del cliente desde los metadatos del usuario
    $billing_first_name = get_user_meta($user_id, 'billing_first_name', true);
    $billing_last_name = get_user_meta($user_id, 'billing_last_name', true);
    $billing_email = get_user_meta($user_id, 'billing_email', true);
    $billing_phone = get_user_meta($user_id, 'billing_phone', true);
    $billing_address_1 = get_user_meta($user_id, 'billing_address_1', true);
    $billing_city = get_user_meta($user_id, 'billing_city', true);
    $billing_postcode = get_user_meta($user_id, 'billing_postcode', true);
    $billing_country = get_user_meta($user_id, 'billing_country', true);
    
    // Si no hay datos en los metadatos del usuario, usar los de la reserva
    if (!$billing_first_name) {
        $billing_first_name = get_post_meta($post_id, '_billing_first_name', true);
    }
    if (!$billing_last_name) {
        $billing_last_name = get_post_meta($post_id, '_billing_last_name', true);
    }
    if (!$billing_email) {
        $billing_email = get_post_meta($post_id, '_billing_email', true);
    }
    if (!$billing_phone) {
        $billing_phone = get_post_meta($post_id, '_billing_phone', true);
    }
    if (!$billing_address_1) {
        $billing_address_1 = get_post_meta($post_id, '_billing_address_1', true);
    }
    if (!$billing_city) {
        $billing_city = get_post_meta($post_id, '_billing_city', true);
    }
    if (!$billing_postcode) {
        $billing_postcode = get_post_meta($post_id, '_billing_postcode', true);
    }
    if (!$billing_country) {
        $billing_country = get_post_meta($post_id, '_billing_country', true);
    }
    
    // Fallback a datos del usuario si no hay nada
    if (!$billing_first_name) {
        $billing_first_name = $user_info->first_name ?: '';
    }
    if (!$billing_last_name) {
        $billing_last_name = $user_info->last_name ?: '';
    }
    if (!$billing_email) {
        $billing_email = $user_info->user_email;
    }
    
    $product = wc_get_product($product_id);
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
    
    // Crear la orden hija con título que incluya el producto
    $data_hija = $data;
    $product_name = $product ? $product->get_name() : 'Refugi';
    $data_hija["post_title"] = 'Order &ndash; ' . date('F j, Y @ h:i A', $timestamp) . ' ( en ' . $product_name . ' )';
    $data_hija["post_name"] = sanitize_title('Order &ndash; ' . date('F j, Y @ h:i A', $timestamp) . ' en ' . $product_name);
    $post_id_shop_2 = wp_insert_post($data_hija);
    $updated_post_shop_1 = array(
       'ID'     => $post_id_shop_1,
       'guid'   => $url.'?post_type=yith_booking&p='.$post_id_shop_1,
       'post_title' => 'Order &ndash; ' . date('F j, Y @ h:i A', $timestamp) . ' ( en ' . $product_name . ' )',
       'post_name' => sanitize_title('Order &ndash; ' . date('F j, Y @ h:i A', $timestamp) . ' en ' . $product_name),
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
        'order_item_name' => $product ? $product->name : 'Refugi',
        'order_item_type' => 'line_item',
        'order_id'        => $post_id_shop_2,
    ), array('%s', '%s', '%d'));
    $new_order_id = $wpdb->insert_id;
    
    // Crear el item de impuestos
    $wpdb->insert('wpol_woocommerce_order_items', array(
        'order_item_name' => 'ES-10% IVA-1',
        'order_item_type' => 'tax',
        'order_id'        => $post_id_shop_2,
    ), array('%s', '%s', '%d'));
    $tax_order_item_id = $wpdb->insert_id;
    
    // Crear items en la orden padre (shop_1)
    $wpdb->insert('wpol_woocommerce_order_items', array(
        'order_item_name' => $product ? $product->name : 'Refugi',
        'order_item_type' => 'line_item',
        'order_id'        => $post_id_shop_1,
    ), array('%s', '%s', '%d'));
    $parent_order_item_id = $wpdb->insert_id;
    
    // Crear el item de impuestos en la orden padre
    $wpdb->insert('wpol_woocommerce_order_items', array(
        'order_item_name' => 'ES-10% IVA-1',
        'order_item_type' => 'tax',
        'order_id'        => $post_id_shop_1,
    ), array('%s', '%s', '%d'));
    $parent_tax_order_item_id = $wpdb->insert_id;
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
    
    // Obtener las fechas de la reserva
    $from = get_post_meta($post_id, '_from', true);
    $to = get_post_meta($post_id, '_to', true);
    $duration = get_post_meta($post_id, '_duration', true);
    $persons = get_post_meta($post_id, '_persons', true);
    
    // Convertir timestamps a fechas si es necesario
    if (is_numeric($from)) {
        $from = date('Y-m-d', $from);
    }
    if (is_numeric($to)) {
        $to = date('Y-m-d', $to);
    }
    
    // Agregar todos los metadatos del item de orden según la estructura de la base de datos
    wc_add_order_item_meta($new_order_id, '_product_id', $product ? $product->get_id() : 0);
    wc_add_order_item_meta($new_order_id, '_variation_id', 0);
    wc_add_order_item_meta($new_order_id, '_qty', 1);
    wc_add_order_item_meta($new_order_id, '_tax_class', '');
    
    // Calcular subtotales y totales
    $amount = $params['payment']['duplication']['currentAmount'] ?? 0;
    $tax_rate = 0.10; // 10% IVA
    $subtotal = $amount / (1 + $tax_rate);
    $tax_amount = $amount - $subtotal;
    
    wc_add_order_item_meta($new_order_id, '_line_subtotal', number_format($subtotal, 6));
    wc_add_order_item_meta($new_order_id, '_line_subtotal_tax', number_format($tax_amount, 2));
    wc_add_order_item_meta($new_order_id, '_line_total', number_format($subtotal, 6));
    wc_add_order_item_meta($new_order_id, '_line_tax', number_format($tax_amount, 2));
    
    // Datos de impuestos serializados
    $tax_data = array(
        'total' => array(1 => number_format($tax_amount, 6)),
        'subtotal' => array(1 => number_format($tax_amount, 6))
    );
    wc_add_order_item_meta($new_order_id, '_line_tax_data', $tax_data);
    
    // Datos de reserva serializados
    $booking_data = array(
        'from' => strtotime($from),
        'to' => strtotime($to),
        'duration' => $duration ?: 1,
        'persons' => $persons ?: 1,
        'person_types' => array(30748 => strval($persons ?: 1)),
        'booking_services' => array(),
        'booking_service_quantities' => array(),
        'resource_ids' => array(),
        '_added-to-cart-timestamp' => time()
    );
    wc_add_order_item_meta($new_order_id, 'yith_booking_data', $booking_data);
    
    // Datos de depósito
    wc_add_order_item_meta($new_order_id, '_deposit', 1);
    wc_add_order_item_meta($new_order_id, '_deposit_type', 'rate');
    wc_add_order_item_meta($new_order_id, '_deposit_amount', $amount);
    wc_add_order_item_meta($new_order_id, '_deposit_rate', 100);
    wc_add_order_item_meta($new_order_id, '_deposit_value', $amount);
    wc_add_order_item_meta($new_order_id, '_deposit_balance', 0);
    wc_add_order_item_meta($new_order_id, '_deposit_balance_shipping', '');
    
    // Datos del producto para YWPI
    wc_add_order_item_meta($new_order_id, '_ywpi_product_regular_price', '');
    wc_add_order_item_meta($new_order_id, '_ywpi_product_sku', $product ? $product->get_sku() : '');
    wc_add_order_item_meta($new_order_id, '_ywpi_product_short_description', $product ? $product->get_short_description() : '');
    
    // ID de la reserva
    wc_add_order_item_meta($new_order_id, '_booking_id', $post_id);
    wc_add_order_item_meta($new_order_id, '_reduced_stock', '1');
    
    // Datos de comisión (si aplica)
    wc_add_order_item_meta($new_order_id, '_commission_id', '');
    wc_add_order_item_meta($new_order_id, '_commission_included_tax', 'vendor');
    wc_add_order_item_meta($new_order_id, '_commission_included_coupon', 'yes');
    
    // Agregar metadatos del item de impuestos
    wc_add_order_item_meta($tax_order_item_id, 'rate_id', '1');
    wc_add_order_item_meta($tax_order_item_id, 'label', '10% IVA');
    wc_add_order_item_meta($tax_order_item_id, 'compound', '');
    wc_add_order_item_meta($tax_order_item_id, 'tax_amount', number_format($tax_amount, 2));
    wc_add_order_item_meta($tax_order_item_id, 'shipping_tax_amount', '0');
    wc_add_order_item_meta($tax_order_item_id, 'rate_percent', '10');
    
    // Agregar metadatos del item principal en la orden padre
    wc_add_order_item_meta($parent_order_item_id, '_product_id', $product ? $product->get_id() : 0);
    wc_add_order_item_meta($parent_order_item_id, '_variation_id', 0);
    wc_add_order_item_meta($parent_order_item_id, '_qty', 1);
    wc_add_order_item_meta($parent_order_item_id, '_tax_class', '');
    wc_add_order_item_meta($parent_order_item_id, '_line_subtotal', number_format($subtotal, 6));
    wc_add_order_item_meta($parent_order_item_id, '_line_subtotal_tax', number_format($tax_amount, 2));
    wc_add_order_item_meta($parent_order_item_id, '_line_total', number_format($subtotal, 6));
    wc_add_order_item_meta($parent_order_item_id, '_line_tax', number_format($tax_amount, 2));
    wc_add_order_item_meta($parent_order_item_id, '_line_tax_data', $tax_data);
    wc_add_order_item_meta($parent_order_item_id, '_parent_line_item_id', $new_order_id); // Referencia al item de la orden hija
    wc_add_order_item_meta($parent_order_item_id, 'yith_booking_data', $booking_data);
    wc_add_order_item_meta($parent_order_item_id, '_deposit', 1);
    wc_add_order_item_meta($parent_order_item_id, '_deposit_type', 'rate');
    wc_add_order_item_meta($parent_order_item_id, '_deposit_amount', $amount);
    wc_add_order_item_meta($parent_order_item_id, '_deposit_rate', 100);
    wc_add_order_item_meta($parent_order_item_id, '_deposit_value', $amount);
    wc_add_order_item_meta($parent_order_item_id, '_deposit_balance', 0);
    wc_add_order_item_meta($parent_order_item_id, '_deposit_balance_shipping', '');
    wc_add_order_item_meta($parent_order_item_id, '_ywpi_product_regular_price', '');
    wc_add_order_item_meta($parent_order_item_id, '_ywpi_product_sku', $product ? $product->get_sku() : '');
    wc_add_order_item_meta($parent_order_item_id, '_ywpi_product_short_description', $product ? $product->get_short_description() : '');
    wc_add_order_item_meta($parent_order_item_id, '_booking_id', $post_id);
    wc_add_order_item_meta($parent_order_item_id, '_reduced_stock', '1');
    wc_add_order_item_meta($parent_order_item_id, '_commission_id', '');
    wc_add_order_item_meta($parent_order_item_id, '_commission_included_tax', 'vendor');
    
    // Agregar metadatos del item de impuestos en la orden padre
    wc_add_order_item_meta($parent_tax_order_item_id, 'rate_id', '1');
    wc_add_order_item_meta($parent_tax_order_item_id, 'label', '10% IVA');
    wc_add_order_item_meta($parent_tax_order_item_id, 'compound', '');
    wc_add_order_item_meta($parent_tax_order_item_id, 'tax_amount', number_format($tax_amount, 2));
    wc_add_order_item_meta($parent_tax_order_item_id, 'shipping_tax_amount', '0');
    wc_add_order_item_meta($parent_tax_order_item_id, 'rate_percent', '10');
    
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
    update_post_meta($post_id_shop_2, '_billing_first_name', $billing_first_name);
    update_post_meta($post_id_shop_2, '_billing_last_name', $billing_last_name);
    update_post_meta($post_id_shop_2, '_billing_address_1', $billing_address_1 ?: '');
    update_post_meta($post_id_shop_2, '_billing_city', $billing_city ?: '');
    update_post_meta($post_id_shop_2, '_billing_state', "");
    update_post_meta($post_id_shop_2, '_billing_postcode', $billing_postcode ?: '');
    update_post_meta($post_id_shop_2, '_billing_country', $billing_country ?: 'ES'); 
    update_post_meta($post_id_shop_2, '_billing_email', $billing_email);
    update_post_meta($post_id_shop_2, '_billing_phone', $billing_phone ?: ''); 
    
    // Actualizar metadatos de la orden padre (necesario para el calendario)
    update_post_meta($post_id_shop_1, '_billing_first_name', $billing_first_name);
    update_post_meta($post_id_shop_1, '_billing_last_name', $billing_last_name);
    update_post_meta($post_id_shop_1, '_billing_address_1', $billing_address_1 ?: '');
    update_post_meta($post_id_shop_1, '_billing_city', $billing_city ?: '');
    update_post_meta($post_id_shop_1, '_billing_state', "");
    update_post_meta($post_id_shop_1, '_billing_postcode', $billing_postcode ?: '');
    update_post_meta($post_id_shop_1, '_billing_country', $billing_country ?: 'ES');
    update_post_meta($post_id_shop_1, '_billing_email', $billing_email);
    update_post_meta($post_id_shop_1, '_billing_phone', $billing_phone ?: '');
    
    // Actualizar metadatos de la reserva principal para que aparezcan en el calendario
    update_post_meta($post_id, '_billing_first_name', $billing_first_name);
    update_post_meta($post_id, '_billing_last_name', $billing_last_name);
    update_post_meta($post_id, '_billing_email', $billing_email);
    update_post_meta($post_id, '_billing_phone', $billing_phone ?: '');
    
    update_post_meta($post_id_shop_2, '_order_currency', "EUR");
    update_post_meta($post_id_shop_2, '_cart_discount', 0); 
    update_post_meta($post_id_shop_2, '_cart_discount_tax', "0");
    update_post_meta($post_id_shop_2, '_order_shipping', 0); 
    update_post_meta($post_id_shop_2, '_order_shipping_tax', 0); 
    update_post_meta($post_id_shop_2, '_order_tax', number_format($tax_amount, 2)); 
    update_post_meta($post_id_shop_2, '_order_total', $params['payment']['duplication']['currentAmount'] ?? 0);
    update_post_meta($post_id_shop_2, '_prices_include_tax', "yes");
    update_post_meta($post_id_shop_2, '_billing_address_index', $billing_first_name . ' ' . $billing_last_name . ' ' . ($billing_address_1 ?: '') . ' ' . ($billing_city ?: '') . ' ' . ($billing_postcode ?: '') . ' ' . ($billing_country ?: 'ES') . ' ' . $billing_email . ' ' . ($billing_phone ?: ''));
    update_post_meta($post_id_shop_2, '_shipping_address_index', '');
    update_post_meta($post_id_shop_2, '_billing_Dni_·_Nie_·_Passaport', '');
    update_post_meta($post_id_shop_2, '_billing_Província', '');
    update_post_meta($post_id_shop_2, 'is_vat_exempt', "no");
    update_post_meta($post_id_shop_2, 'additional_Federació', "");
    update_post_meta($post_id_shop_2, 'additional_sortidatravessa', '');
    update_post_meta($post_id_shop_2, '_ywson_custom_number_order_complete', 'CODI-' . ($params['payment']['duplication']['bk_transaction_id'] ?? uniqid()));
    update_post_meta($post_id_shop_2, 'trp_language', 'ca');
    update_post_meta($post_id_shop_2, '_wc_order_attribution_source_type', 'referral');
    update_post_meta($post_id_shop_2, '_wc_order_attribution_device_type', 'Mobile');
    update_post_meta($post_id_shop_2, '_wc_order_attribution_referrer', 'xSalto Payment System');
    update_post_meta($post_id_shop_2, '_wc_order_attribution_utm_source', 'xSalto');
    update_post_meta($post_id_shop_2, '_wc_order_attribution_utm_medium', 'referral');
    update_post_meta($post_id_shop_2, '_wc_order_attribution_utm_content', 'payment');
    update_post_meta($post_id_shop_2, '_wc_order_attribution_session_entry', 'xSalto Payment Gateway');
    update_post_meta($post_id_shop_2, '_wc_order_attribution_session_start_time', $date);
    update_post_meta($post_id_shop_2, '_wc_order_attribution_session_pages', '1');
    update_post_meta($post_id_shop_2, '_wc_order_attribution_session_count', '1');
    update_post_meta($post_id_shop_2, '_wc_order_attribution_user_agent', 'xSalto Payment System');
    update_post_meta($post_id_shop_2, '_has_deposit', 1);
    update_post_meta($post_id_shop_2, 'yith_bookings', 'a:1:{i:0;i:'.$post_id.';}');
    update_post_meta($post_id_shop_2, '_ywson_subnumber_created', "yes");
    update_post_meta($post_id_shop_2, '_commissions_processed', 'yes');
    update_post_meta($post_id_shop_2, '_date_completed', strtotime($date));
    update_post_meta($post_id_shop_2, '_date_paid', strtotime($date));
    update_post_meta($post_id_shop_2, '_paid_date', $date);
    update_post_meta($post_id_shop_2, '_completed_date', $date); 
    update_post_meta($post_id_shop_2, '_order_version', "8.0.0"); 
    update_post_meta($post_id_shop_2, 'vendor_id', $user_display_id); 
    
    // Agregar metadatos para la orden padre también
    update_post_meta($post_id_shop_1, '_order_key', 'wc_order_'.uniqid());
    update_post_meta($post_id_shop_1, '_customer_user', $user_id);
    update_post_meta($post_id_shop_1, '_payment_method', 'xsalto');
    update_post_meta($post_id_shop_1, '_payment_method_title', 'xSalto');
    update_post_meta($post_id_shop_1, '_customer_ip_address', 0);
    update_post_meta($post_id_shop_1, '_customer_user_agent', 'Mozilla/5.0');
    update_post_meta($post_id_shop_1, '_created_via', "yith_wcmv_vendor_suborder");
    update_post_meta($post_id_shop_1, '_download_permissions_granted', "yes");
    update_post_meta($post_id_shop_1, '_recorded_sales', 'yes');
    update_post_meta($post_id_shop_1, '_recorded_coupon_usage_counts', "no");
    update_post_meta($post_id_shop_1, '_new_order_email_sent', "no");
    update_post_meta($post_id_shop_1, '_order_stock_reduced', "yes");
    update_post_meta($post_id_shop_1, '_order_currency', "EUR");
    update_post_meta($post_id_shop_1, '_cart_discount', 0); 
    update_post_meta($post_id_shop_1, '_cart_discount_tax', "0");
    update_post_meta($post_id_shop_1, '_order_shipping', 0); 
    update_post_meta($post_id_shop_1, '_order_shipping_tax', 0); 
    update_post_meta($post_id_shop_1, '_order_tax', number_format($tax_amount, 2)); 
    update_post_meta($post_id_shop_1, '_order_total', $params['payment']['duplication']['currentAmount'] ?? 0);
    update_post_meta($post_id_shop_1, '_order_version', "8.0.0"); 
    update_post_meta($post_id_shop_1, '_prices_include_tax', "yes");
    update_post_meta($post_id_shop_1, '_billing_address_index', $billing_first_name . ' ' . $billing_last_name . ' ' . ($billing_address_1 ?: '') . ' ' . ($billing_city ?: '') . ' ' . ($billing_postcode ?: '') . ' ' . ($billing_country ?: 'ES') . ' ' . $billing_email . ' ' . ($billing_phone ?: ''));
    update_post_meta($post_id_shop_1, '_shipping_address_index', '');
    update_post_meta($post_id_shop_1, '_billing_Dni_·_Nie_·_Passaport', '');
    update_post_meta($post_id_shop_1, '_billing_Província', '');
    update_post_meta($post_id_shop_1, 'is_vat_exempt', "no");
    update_post_meta($post_id_shop_1, 'additional_Federació', "");
    update_post_meta($post_id_shop_1, 'additional_sortidatravessa', '');
    update_post_meta($post_id_shop_1, '_ywson_custom_number_order_complete', 'CODI-' . ($params['payment']['duplication']['bk_transaction_id'] ?? uniqid()));
    update_post_meta($post_id_shop_1, 'trp_language', 'ca');
    update_post_meta($post_id_shop_1, '_wc_order_attribution_source_type', 'referral');
    update_post_meta($post_id_shop_1, '_wc_order_attribution_device_type', 'Mobile');
    update_post_meta($post_id_shop_1, '_wc_order_attribution_referrer', 'xSalto Payment System');
    update_post_meta($post_id_shop_1, '_wc_order_attribution_utm_source', 'xSalto');
    update_post_meta($post_id_shop_1, '_wc_order_attribution_utm_medium', 'referral');
    update_post_meta($post_id_shop_1, '_wc_order_attribution_utm_content', 'payment');
    update_post_meta($post_id_shop_1, '_wc_order_attribution_session_entry', 'xSalto Payment Gateway');
    update_post_meta($post_id_shop_1, '_wc_order_attribution_session_start_time', $date);
    update_post_meta($post_id_shop_1, '_wc_order_attribution_session_pages', '1');
    update_post_meta($post_id_shop_1, '_wc_order_attribution_session_count', '1');
    update_post_meta($post_id_shop_1, '_wc_order_attribution_user_agent', 'xSalto Payment System');
    update_post_meta($post_id_shop_1, '_has_deposit', 1);
    update_post_meta($post_id_shop_1, 'yith_bookings', 'a:1:{i:0;i:'.$post_id.';}');
    update_post_meta($post_id_shop_1, '_ywson_subnumber_created', "yes");
    update_post_meta($post_id_shop_1, '_commissions_processed', 'yes');
    update_post_meta($post_id_shop_1, '_date_completed', strtotime($date));
    update_post_meta($post_id_shop_1, '_date_paid', strtotime($date));
    update_post_meta($post_id_shop_1, '_paid_date', $date);
    update_post_meta($post_id_shop_1, '_completed_date', $date);  
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
    
    // 4.1. Actualizar metadato _person_types con datos serializados
    $data = array(
        array(
            'id' => 0,
            'title' => 'Nº de Persones',
            'number' => strval($qty)
        )
    );
    $serialized_data = $data;
    update_post_meta($post_id, '_person_types', $serialized_data);
    
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
        // Buscar órdenes padre e hija
        $parent_order_id = $order_id;
        $child_order_id = $order_id;
        
        // Verificar si es una orden hija (tiene padre)
        $parent_check = $wpdb->get_var($wpdb->prepare("SELECT post_parent FROM {$wpdb->prefix}posts WHERE ID = %d", $order_id));
        if ($parent_check > 0) {
            // Es una orden hija, el padre es $parent_check
            $parent_order_id = $parent_check;
            $child_order_id = $order_id;
        } else {
            // Es una orden padre, buscar la hija
            $child_order_id = $wpdb->get_var($wpdb->prepare("SELECT ID FROM {$wpdb->prefix}posts WHERE post_parent = %d AND post_type = 'shop_order'", $order_id));
        }
        
        // Eliminar items de WooCommerce de ambas órdenes
        if ($parent_order_id) {
            $parent_items = $wpdb->get_col($wpdb->prepare("SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d", $parent_order_id));
            foreach ($parent_items as $item_id) {
                // Eliminar metadatos del item
                $wpdb->delete($wpdb->prefix . 'woocommerce_order_itemmeta', array('order_item_id' => $item_id));
            }
            // Eliminar items
            $wpdb->delete($wpdb->prefix . 'woocommerce_order_items', array('order_id' => $parent_order_id));
            // Eliminar metadatos de la orden padre
            $wpdb->delete($wpdb->prefix . 'postmeta', array('post_id' => $parent_order_id));
            // Eliminar la orden padre
            wp_delete_post($parent_order_id, true);
        }
        
        if ($child_order_id && $child_order_id != $parent_order_id) {
            $child_items = $wpdb->get_col($wpdb->prepare("SELECT order_item_id FROM {$wpdb->prefix}woocommerce_order_items WHERE order_id = %d", $child_order_id));
            foreach ($child_items as $item_id) {
                // Eliminar metadatos del item
                $wpdb->delete($wpdb->prefix . 'woocommerce_order_itemmeta', array('order_item_id' => $item_id));
            }
            // Eliminar items
            $wpdb->delete($wpdb->prefix . 'woocommerce_order_items', array('order_id' => $child_order_id));
            // Eliminar metadatos de la orden hija
            $wpdb->delete($wpdb->prefix . 'postmeta', array('post_id' => $child_order_id));
            // Eliminar la orden hija
            wp_delete_post($child_order_id, true);
        }
        
        // Eliminar estadísticas de WooCommerce
        $wpdb->delete($wpdb->prefix . 'wc_order_stats', array('order_id' => $parent_order_id));
        if ($child_order_id != $parent_order_id) {
            $wpdb->delete($wpdb->prefix . 'wc_order_stats', array('order_id' => $child_order_id));
        }
    }

    // 3. Eliminar metadatos de la reserva
    $wpdb->delete($wpdb->prefix . 'postmeta', array('post_id' => $booking_id));
    
    // 4. Eliminar de la tabla de reservas
    $wpdb->delete($wpdb->prefix . 'yith_wcbk_booking_meta_lookup', array('booking_id' => $booking_id));
    
    // 5. Eliminar post principal (reserva)
    $deleted = wp_delete_post($booking_id, true);

    if ($deleted) {
        return ['response' => true];
    } else {
        return new WP_Error('delete_failed', 'No se pudo eliminar la reserva', array('status' => 500));
    }
}