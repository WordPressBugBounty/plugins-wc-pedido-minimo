<?php
function wc_pedido_minimo_get_current_role() {
  if ( is_user_logged_in() ) {
    $user = wp_get_current_user();
    $role = (array) $user->roles;
    return $role[0];
  }
  return false;
}

function wc_pedido_minimo_violation_message() {
    $pedido_minimo_onoff = get_option('wc-pedido-minimo-onoff','no');
    if ($pedido_minimo_onoff !== 'yes') return '';
    $pedido_minimo_usuarios = get_option('wc-pedido-minimo-usuarios','');
    $wc_role = wc_pedido_minimo_get_current_role();
    if ($pedido_minimo_usuarios != 0 && $pedido_minimo_usuarios !== $wc_role) return '';

    $pedido_minimo_funcionamento = get_option('wc-pedido-minimo-funcionamento','valor');
    $pedido_minimo_valor = is_numeric(get_option('wc-pedido-minimo-valor','0')) ? (float) get_option('wc-pedido-minimo-valor','0') : 0;
    $pedido_minimo_quantidade = is_numeric(get_option('wc-pedido-minimo-quantidade','0')) ? (int) get_option('wc-pedido-minimo-quantidade','0') : 0;

    if ( ! WC()->cart ) return '';
    $total_carrinho_valor = (float) WC()->cart->get_displayed_subtotal();
    $total_carrinho_quantidade = (int) WC()->cart->get_cart_contents_count();

    if ( $pedido_minimo_funcionamento === 'valor' && $pedido_minimo_valor > 0 && $total_carrinho_valor < $pedido_minimo_valor ) {
        $saldo = wc_price($pedido_minimo_valor - $total_carrinho_valor);
        return esc_html__('O Pedido deve ter o valor mínimo de', 'wc-pedido-minimo').' '.wc_price($pedido_minimo_valor).'. '.esc_html__('O Valor total do seu pedido agora é de', 'wc-pedido-minimo').' '.wc_price($total_carrinho_valor).'. '.sprintf(__('Faltam %s para atingir o valor mínimo da loja', 'wc-pedido-minimo'), $saldo).'.';
    }

    if ( $pedido_minimo_funcionamento === 'quantidade' && $pedido_minimo_quantidade > 0 && $total_carrinho_quantidade < $pedido_minimo_quantidade ) {
        $saldo = $pedido_minimo_quantidade - $total_carrinho_quantidade;
        $txtSaldo = $saldo == 1 ? __( 'item', 'wc-pedido-minimo' ) : __( 'itens', 'wc-pedido-minimo' );
        $txtAtual = $total_carrinho_quantidade == 1 ? __( 'item', 'wc-pedido-minimo' ) : __( 'itens', 'wc-pedido-minimo' );
        return sprintf(esc_html__('O Pedido deve ter a quantidade mínima de', 'wc-pedido-minimo').' %s itens. '.esc_html__('Seu pedido agora possui', 'wc-pedido-minimo').' %s %s. '.esc_html__('Faltam', 'wc-pedido-minimo').' %s %s.', $pedido_minimo_quantidade, $total_carrinho_quantidade, $txtAtual, $saldo, $txtSaldo);
    }

    return '';
}

function wc_pedido_minimo_function() {
    if ( ! ( is_cart() || is_checkout() ) ) return;

    $pedido_minimo_onoff = get_option( 'wc-pedido-minimo-onoff', 'no' );
    $pedido_minimo_usuarios = get_option( 'wc-pedido-minimo-usuarios', '' );
    $wc_role = wc_pedido_minimo_get_current_role();
    if ( $pedido_minimo_onoff !== 'yes' || ( $pedido_minimo_usuarios != 0 && $pedido_minimo_usuarios !== $wc_role ) ) return;

    if ( ! WC()->cart ) return;
    $total_carrinho_valor = (float) WC()->cart->get_displayed_subtotal();
    $total_carrinho_quantidade = (int) WC()->cart->get_cart_contents_count();

    $pedido_minimo_funcionamento = get_option( 'wc-pedido-minimo-funcionamento', 'valor' );
    $pedido_minimo_valor = is_numeric( get_option( 'wc-pedido-minimo-valor', '0' ) ) ? (float) get_option( 'wc-pedido-minimo-valor', '0' ) : 0;
    $pedido_minimo_quantidade = is_numeric( get_option( 'wc-pedido-minimo-quantidade', '0' ) ) ? (int) get_option( 'wc-pedido-minimo-quantidade', '0' ) : 0;

    $msg = wc_pedido_minimo_violation_message();
    if ( $msg ) wc_add_notice( $msg, 'error' );

    if ( $pedido_minimo_funcionamento === 'valor' && $pedido_minimo_valor > 0 && $total_carrinho_valor >= $pedido_minimo_valor ) {
        wc_add_notice( esc_html__( 'Parabéns! Seu pedido atingiu o valor mínimo estabelecido.', 'wc-pedido-minimo' ), 'success' );
    }

    if ( $pedido_minimo_funcionamento === 'quantidade' && $pedido_minimo_quantidade > 0 && $total_carrinho_quantidade >= $pedido_minimo_quantidade ) {
        wc_add_notice( esc_html__( 'Parabéns! Seu pedido atingiu a quantidade mínima estabelecida.', 'wc-pedido-minimo' ), 'success' );
    }
}
add_action( 'woocommerce_check_cart_items', 'wc_pedido_minimo_function' );
add_action( 'woocommerce_checkout_process', 'wc_pedido_minimo_function' );

function wc_pedido_minimo_checkout_blocker( $data, $errors ) {
    $msg = wc_pedido_minimo_violation_message();
    if ( $msg ) $errors->add( 'wc_pedido_minimo', $msg );
}
add_action( 'woocommerce_after_checkout_validation', 'wc_pedido_minimo_checkout_blocker', 10, 2 );

function wc_pedido_minimo_store_api_blocker( $errors, $request ) {
    $msg = wc_pedido_minimo_violation_message();
    if ( $msg ) $errors->add( 'wc_pedido_minimo', $msg );
    return $errors;
}
add_action( 'woocommerce_store_api_cart_errors', 'wc_pedido_minimo_store_api_blocker', 10, 2 );

function woocommerce_pedido_minimo_payment_method( $data, $errors ) {
    if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;
    $required = get_option( 'wc-pedido-minimo-pagamentos', [] );
    if ( empty( $required ) || ! is_array( $required ) ) return;

    $gateways = WC()->payment_gateways->get_available_payment_gateways();
    if ( empty( $gateways ) ) return;

    $available_ids = array_keys( $gateways );
    $allowed_available = array_values( array_intersect( $available_ids, $required ) );
    if ( empty( $allowed_available ) ) return;
    if ( count( $available_ids ) === 1 && in_array( $available_ids[0], $allowed_available, true ) ) return;

    $chosen = '';
    if ( is_array( $data ) && ! empty( $data['payment_method'] ) ) {
        $chosen = sanitize_text_field( $data['payment_method'] );
    } elseif ( WC()->session ) {
        $chosen = (string) WC()->session->get( 'chosen_payment_method', '' );
    }

    if ( ! $chosen || ! in_array( $chosen, $required, true ) ) {
        $titles = [];
        foreach ( $allowed_available as $id ) $titles[] = $gateways[$id]->title;
        if ( ! empty( $titles ) ) {
            $msg = sprintf( '%s %s', esc_html__( 'Este pedido requer a(s) seguinte(s) forma(s) de pagamento:', 'wc-pedido-minimo' ), implode( ' / ', $titles ) );
            wc_add_notice( $msg, 'error' );
            $errors->add( 'wc_pedido_minimo_pagamento', $msg );
        }
    }
}
add_action( 'woocommerce_after_checkout_validation', 'woocommerce_pedido_minimo_payment_method', 20, 2 );

function woocommerce_pedido_minimo_payment_method_store_api( $errors, $request ) {
    $required = get_option( 'wc-pedido-minimo-pagamentos', [] );
    if ( empty( $required ) || ! is_array( $required ) ) return $errors;

    $gateways = WC()->payment_gateways->get_available_payment_gateways();
    if ( empty( $gateways ) ) return $errors;

    $available_ids = array_keys( $gateways );
    $allowed_available = array_values( array_intersect( $available_ids, $required ) );
    if ( empty( $allowed_available ) ) return $errors;
    if ( count( $available_ids ) === 1 && in_array( $available_ids[0], $allowed_available, true ) ) return $errors;

    $chosen = '';
    if ( $request instanceof WP_REST_Request ) {
        $chosen = (string) $request->get_param( 'payment_method' );
        if ( ! $chosen ) {
            $pd = $request->get_param( 'payment_data' );
            if ( is_array( $pd ) && ! empty( $pd['payment_method'] ) ) $chosen = (string) $pd['payment_method'];
        }
    }
    if ( ! $chosen && WC()->session ) {
        $chosen = (string) WC()->session->get( 'chosen_payment_method', '' );
    }

    if ( ! in_array( $chosen, $required, true ) ) {
        $titles = [];
        foreach ( $allowed_available as $id ) $titles[] = $gateways[$id]->title;
        if ( ! empty( $titles ) ) {
            $msg = sprintf( '%s %s', esc_html__( 'Este pedido requer a(s) seguinte(s) forma(s) de pagamento:', 'wc-pedido-minimo' ), implode( ' / ', $titles ) );
            $errors->add( 'wc_pedido_minimo_pagamento', $msg );
        }
    }
    return $errors;
}
add_action( 'woocommerce_store_api_cart_errors', 'woocommerce_pedido_minimo_payment_method_store_api', 20, 2 );
