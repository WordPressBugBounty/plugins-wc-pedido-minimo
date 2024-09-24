<?php
/*------------------------------------------------------------------------------------------------------------------
Plugin Name: WooCommerce Pedido Mínimo
Description: Plugin para configurar valor mínimo ou quantidade mínima de items para finalização de pedidos no WooCommerce. O plugin também permite selecionar uma função de usuário do Wordpress e formas de pagamento para aplicar as regras configuradas.
Version: 2.1.0
Author: Daniel Ferraz Ramos
Author URI: http://art2web.com.br/plugin-woocommerce-pedido-minimo/
Text Domain: wc-pedido-minimo
Domain Path: /inc/languages
---------------------------------------------------------------------------------------------------------------------*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Sair se for acessado diretamente.
}

if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
	define('PEDIDOMINIMO_PLUGIN_URL', plugins_url('', __FILE__));
	define('PEDIDOMINIMO_PLUGIN_DIR', plugin_dir_path(__FILE__));

	require_once( PEDIDOMINIMO_PLUGIN_DIR . '/inc/load-assets.php');
	require_once( PEDIDOMINIMO_PLUGIN_DIR . '/inc/load-admin-settings.php');
	require_once( PEDIDOMINIMO_PLUGIN_DIR . '/inc/load-pedido-minimo.php');
} else {
	exit;
}


function wc_pedido_minimo_text_domain_init() {
    $pedido_minimo_rel_path = PEDIDOMINIMO_PLUGIN_DIR . '/languages/';
    load_plugin_textdomain( 'wc-pedido-minimo', false, $pedido_minimo_rel_path );
}
add_action('plugins_loaded', 'wc_pedido_minimo_text_domain_init');
