<?php
/*
Plugin Name:    Woo Quick Phone Order
Plugin URI:     http://dfdesign.net
Description:    Quick phone orders for your Woocommerce Shop. Аllow your customers to order quickly with just 1 click. This will increase the number of orders placed in your store and customer satisfaction.
Version:        0.1
Author:         V. Dafinov
Author URI:
License:        GPLv2
*/

require_once('admin-options.php');
if( is_admin() )
    $my_settings_page = new WooQuickPhoneOrderAdminPage();

add_action( 'woocommerce_after_add_to_cart_form', 'show_wqpo_form' );

function show_wqpo_form() {
    global $product;
    $product_id = $product->get_id();

    echo '
	<div class="wqpo-form">
		<div id="mask">
			<img src="'. admin_url( 'images/wpspin_light.gif' ) . '" >
		</div>
		<div>' .  __( 'Quick Phone Order', 'quick-order' ) . '</div>
		<input id="wqpo-submit-btn" type="submit" value="' .  __( 'Send', 'quick-order' ) . '">
		<input id="wqpo-phone-filed" type="text" placeholder="' .  __( 'Phone Number', 'quick-order' ) . '">
		<div id="msg"></div>
	</div>
    <style>
        .success{
            background: green
        } 
        .error{
            background: red
        } 
        .wqpo-form{
            clear: both;
            padding: 10px;
            background: ' . get_option("wqpo_options")["button_color"] . ';
            overflow: hidden;
            margin-top: 1em;
            position: relative;
        }
        #wqpo-submit-btn{
            float: right;
            width: 33%;
            height:52px;
            background: ' . get_option("wqpo_options")["box_color"] . ';
        }
        #wqpo-phone-filed{
            float: left;
            width: 65%;
            height: 52px
        }
        #msg{
            color: #fff;
            border-radius: 7px;
            padding: 0.4em;
            clear: both;
            margin-top: 1em;
            display: none;
            border: none
        }
        #mask{
            background: #fff;
            width: 100%;
            height: 100%;
            position: absolute;
            top: 0px;
            left: 0px;
            display: none
        }
        .success, .error {
            padding: 1em
        }
    </style>

    <script>
        jQuery(document).ready(function($){
            
            jQuery("#wqpo-submit-btn").click(function() {
              jQuery("#mask").css("display", "block");
            });
            
            jQuery("#wqpo-submit-btn").click(function(e){
                e.preventDefault();
                var data = {
                    action: "my_action",
                    _ajax_nonce: "'. wp_create_nonce( 'fast_order_nonce' ) .'",
                    product_id: '.$product_id.',
                    phone_num: jQuery("#wqpo-phone-filed").val()
                };
        
                // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php
                jQuery.post("' . admin_url('admin-ajax.php') . '", data, function(response) {
                    jQuery("#msg").css("display", "inline-flex");
                    jQuery("#msg").empty().append(response);
                    jQuery("#mask").css("display", "block");
                }).fail(function(response) {
                    jQuery("#msg").css("display", "inline-flex");
                    jQuery("#msg").empty().append(response);
                }).done(function() {
                    jQuery("#mask").css("display", "none");
                });
            });
        });
    </script>';
}

function create_wc_order(  ){

    if (!check_ajax_referer('fast_order_nonce')) {
        wp_send_json_error('Invalid security token sent.');
        wp_die();
    }

    if (empty($_POST["product_id"])) {
        wp_send_json('<div class="error">' . __( "Error. Please contacts us.", "quick-order" ) . '</div>');
        wp_die();
        die();
    }

    if (empty($_POST["phone_num"])) {
        wp_send_json('<div class="error">' . __( "Please fill your phone number.", "quick-order" ) . '</div>');
        wp_die();
        die();
    }

    $product = wc_get_product(sanitize_text_field($_POST['product_id']));
    $phone_num = sanitize_text_field($_POST['phone_num']);

    $gateways = WC()->payment_gateways->get_available_payment_gateways();

    $order = new WC_Order();
    $order->set_created_via('programatically');
    $order->set_currency(get_woocommerce_currency());
    $order->set_prices_include_tax('yes' === get_option('woocommerce_prices_include_tax'));
    $order->add_product($product, 1);
    $order->set_billing_first_name($phone_num);
    $order->calculate_totals();
    $order->update_status('processing');
    $order->save();

    wp_send_json('<div class="success">' . __( "Thank you. Your order has been received.", "quick-order" ) . '</div>');
    wp_die();
    die();
}

add_action( 'wp_ajax_my_action', 'create_wc_order' );
add_action( 'wp_ajax_nopriv_my_action', 'create_wc_order' );