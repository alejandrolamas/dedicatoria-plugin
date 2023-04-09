<?php
/*
Plugin Name: Dedicatoria Plugin
Plugin URI: https://gratumcorp.com
Description: Añade un campo "Dedicatoria" en el proceso de pago de WooCommerce y muestra su valor en la ficha del pedido y en el correo electrónico de confirmación.
Version: 1.3
Author: Alejandro Lamas
Author URI: https://gratumcorp.com
*/

/* DEDICATORIA */
add_action( 'woocommerce_after_order_notes', 'dedicatoria_checkout_field' );
function dedicatoria_checkout_field( $checkout ) {
	echo '<div id="dedicatoria_checkout_field"><h3>' . __('Dedicatoria') . '</h3>';
	echo '<p>Ingrese aquí su dedicatoria (máximo 250 palabras)</p>';
	woocommerce_form_field( 'dedicatoria', array(
		'type'          => 'textarea',
		'class'         => array('dedicatoria-field form-row-wide'),
		'label'         => __(''),
		'placeholder'   => __('Ingrese su dedicatoria aquí.'),
		'maxlength'		=> 500,
		'required'      => false,
	), $checkout->get_value( 'dedicatoria' ));
	echo '</div>';
}


// Guarda el valor del campo de dedicatoria en el pedido
add_action('woocommerce_checkout_update_order_meta', 'dedicatoria_checkout_field_update_order_meta');
function dedicatoria_checkout_field_update_order_meta($order_id) {
	if ($_POST['dedicatoria']) {
		$dedicatoria = wp_strip_all_tags($_POST['dedicatoria']);
		$dedicatoria = wp_trim_words($dedicatoria, 250, '');
		update_post_meta($order_id, 'Dedicatoria', esc_attr($dedicatoria));
	}
}


// Muestra el valor del campo de dedicatoria en la ficha del pedido y permite imprimir en formato carta
add_action( 'woocommerce_admin_order_data_after_shipping_address', 'dedicatoria_order_meta' );
function dedicatoria_order_meta($order){
    echo '<p><strong>'.__('Dedicatoria').':</strong> ' . get_post_meta( $order->get_id(), 'Dedicatoria', true ) . '</p>';
    echo '<div id="dedicatoria-print" style="display: none;">' . get_post_meta( $order->get_id(), 'Dedicatoria', true ) . '</div>';
    echo '<p><button type="button" id="btn-print-dedicatoria">Imprimir Dedicatoria</button></p>';
    echo '<script type="text/javascript">
        function printDedicatoria() {
            console.log("El botón se ha pulsado");
            var contenido = document.getElementById("dedicatoria-print").innerHTML;
            var ventana = window.open("", "", "height=1000,width=1000");
            ventana.document.write("<html><head>");
            ventana.document.write("<style>@page { size: 11cm 22cm; margin: 0; }</style>");
            ventana.document.write("</head><body style=\'writing-mode: vertical-rl; text-align: justify; font-family: Arial; font-size: 20px; padding: 50px;\'>");
            ventana.document.write(contenido);
            ventana.document.write("</body></html>");
            ventana.document.close();
            ventana.print();
            ventana.close();
            return true;
        }

        jQuery(document).ready(function( $ ){
            $("#btn-print-dedicatoria").click(function(){
                printDedicatoria();
            });
        });
    </script>';
}


add_action( 'wp_enqueue_scripts', 'dedicatoria_print_script' );
function dedicatoria_print_script() {
	wp_enqueue_script( 'jquery-printarea', 'https://cdnjs.cloudflare.com/ajax/libs/jquery-printarea/2.4.1/jquery.PrintArea.min.js', array( 'jquery-core' ), '2.4.1', true );
}