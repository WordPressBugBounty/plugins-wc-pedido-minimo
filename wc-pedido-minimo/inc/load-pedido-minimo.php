<?php
function wc_pedido_minimo_function() {
	global $woocommerce;

	$pedido_minimo_onoff = get_option( 'wc-pedido-minimo-onoff', false );

	$wc_pedido_minimo_get_current_role = wc_pedido_minimo_get_current_role();

	$pedido_minimo_usuarios = get_option( 'wc-pedido-minimo-usuarios', false );

	    if( (is_cart() || is_checkout()) && $pedido_minimo_onoff == 'yes' && ($pedido_minimo_usuarios == '' || $pedido_minimo_usuarios == $wc_pedido_minimo_get_current_role) ) {
	        $total_carrinho_valor = WC()->cart->subtotal;
	        $total_carrinho_quantidade = WC()->cart->get_cart_contents_count();

			$pedido_minimo_funcionamento = get_option( 'wc-pedido-minimo-funcionamento', false );
			$pedido_minimo_valor = get_option( 'wc-pedido-minimo-valor', false );
			$pedido_minimo_quantidade = get_option( 'wc-pedido-minimo-quantidade', false );

	        if( $pedido_minimo_funcionamento == 'valor' ) {
		        if( $total_carrinho_valor < $pedido_minimo_valor ) {
					$saldo = wc_price($pedido_minimo_valor - $total_carrinho_valor);

					$mensagemPedidoMinimo = '<div class="alerta_pedido_minimo"><p>'.esc_html__('O Pedido deve ter o valor mínimo de', 'wc-pedido-minimo').' '.wc_price($pedido_minimo_valor).'</p>';
					$mensagemTotalCarrinho = '<p>'.esc_html__('O Valor total do seu pedido agora é de', 'wc-pedido-minimo').' '.wc_price($total_carrinho_valor).'</p>';
					$mensagemTotalSaldo = '<p>'.sprintf(__( 'Você precisa comprar mais %s para atingir o valor mínimo da loja', 'wc-pedido-minimo'), $saldo).'</p></div>';

					if ( $total_carrinho_valor !== 0 ) {
				            wc_add_notice( $mensagemPedidoMinimo.$mensagemTotalCarrinho.$mensagemTotalSaldo, 'error' );
					}
	        	}
    		} elseif( $pedido_minimo_funcionamento == 'quantidade' ) {
		        if( $total_carrinho_quantidade < $pedido_minimo_quantidade ) {
					$saldo = $pedido_minimo_quantidade - $total_carrinho_quantidade;
					if ( $saldo == 1 ) {
						$txtItem = 'item';
					} elseif ($saldo > 1) {
						$txtItem = 'itens';
					}
					$mensagem = '<p>'.esc_html__( 'Você precisa comprar %s '.$txtItem.' para atingir a quantidade mínima da loja.', 'wc-pedido-minimo').'</p></div>';

					if ( $total_carrinho_quantidade !== 0 ) {
						if ( $total_carrinho_quantidade == 1 ) {
							$txtItem = 'item';
						} else {
							$txtItem = 'itens';
						}

				            wc_add_notice( sprintf( '<div class="alerta_pedido_minimo">
				            	<p>'.esc_html__('O Pedido deve ter a quantidade mínima de', 'wc-pedido-minimo').' <strong>%s itens</strong>.</p>'.'<p>'.esc_html__('Seu pedido agora possui', 'wc-pedido-minimo').' <strong> %s '.$txtItem.'</strong>.</p>'.$mensagem, $pedido_minimo_quantidade, $total_carrinho_quantidade, $saldo ), 'error' );
					}
	        	}
    		}
		}
}
add_action( 'woocommerce_check_cart_items', 'wc_pedido_minimo_function' );


function wc_pedido_minimo_get_current_role() {
  if( is_user_logged_in() ) {
    $user = wp_get_current_user();
    $role = ( array ) $user->roles;
    return $role[0];
  } else {
    return false;
  }
}


function woocommerce_pedido_minimo_payment_method($posted) {
	if ( is_admin() && ! defined( 'DOING_AJAX' ) ) return;

	$pedido_minimo_pagamento = get_option( 'wc-pedido-minimo-pagamentos', false );
	$chosen_payment_method = WC()->session->get('chosen_payment_method');
	$selectedGateways = '';
	foreach($pedido_minimo_pagamento as $payment) {
		if($payment !== $chosen_payment_method && !empty($pedido_minimo_pagamento)) {
			$selected = true;
		} else {
			$selected = false;
		}
	}

	$gateways = WC()->payment_gateways->get_available_payment_gateways();
    $selectedGateways = '';
	$i = 1;
    foreach ($gateways as $gateway) {
        if($gateway->id !== $chosen_payment_method && in_array($gateway->id, $pedido_minimo_pagamento) == -1) {
			$selectedGateways .= $gateway->title;
			if(count($gateways) > 1 && count($gateways)-1 !== $i) {
				$selectedGateways .= ' / ';
			}
		}
		$i++;
    }

	if($selected == true) {
		wc_add_notice( sprintf( '<div class="alerta_pedido_minimo">
		<p>'.esc_html__('Este pedido requer a(s) seguinte(s) forma(s) de pagamento:', 'wc-pedido-minimo').' <strong>'.$selectedGateways.'</strong>.'), 'error' );
		return false;
	} else {
		return;

	}
}
add_action( 'woocommerce_after_checkout_validation','woocommerce_pedido_minimo_payment_method', 20, 1 );
