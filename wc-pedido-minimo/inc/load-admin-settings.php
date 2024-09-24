<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly
}

add_action('admin_head', 'pedido_minimo_admin_css');
function pedido_minimo_admin_css() {
    if (is_admin()) {
        $page = isset($_GET['page']) ? esc_attr($_GET['page']) : '';
        $tab = isset($_GET['tab'])  ? esc_attr($_GET['tab']) : '';
        if ( $page == 'wc-settings' && $tab == 'settings_pedido_minimo_tab' ) {
          echo '
            <style>
                #wc-pedido-minimo-valor,
                #wc-pedido-minimo-quantidade {
                    max-width: 100px;
                    text-align: center;
                }
                #wc-pedido-minimo-funcionamento,
                #wc-pedido-minimo-usuarios {
                    max-width: 250px;
                }
                span.description {
                    float: left;
                    clear: both;
                    width: 100%;
                    margin: 5px 0 0 5px;
                }
                .woocommerce table.form-table th {
                    padding-right: 10px;
                    width: 260px;
                    text-align: right;
                }
                .art2web {
                    position: fixed;
                    top: 92%;
                    right: 10px;
                }
            </style>';

            echo '
                <script type="text/javascript">
                    jQuery(document).ready(function() {
                        if (jQuery("#wc-pedido-minimo-onoff").prop("checked") == false) {
                            jQuery(".wc-pedido-minimo-admin-field").attr("disabled", "disabled");
                        }
                        jQuery("#wc-pedido-minimo-onoff").on("click", function() {
                            if (this.checked) {
                                jQuery(".wc-pedido-minimo-admin-field").removeAttr("disabled");
                            } else {
                                jQuery(".wc-pedido-minimo-admin-field").attr("disabled", "disabled");
                            }
                        });
                        jQuery(window).on("load", function() {
                            jQuery(".button-primary.woocommerce-save-button").after("<div class=art2web><a href=https://art2web.com.br target=_blank><img src='.PEDIDOMINIMO_PLUGIN_URL.'/inc/assets/images/art2web.png></a></div>");
                        });
                    });
                </script>
            ';
        }
    }
}


class WC_Settings_Pedido_Minimo_Tab {


    public static function init() {
        add_filter( 'woocommerce_settings_tabs_array', __CLASS__ . '::add_settings_tab', 50 );
        add_action( 'woocommerce_settings_tabs_settings_pedido_minimo_tab', __CLASS__ . '::settings_tab' );
        add_action( 'woocommerce_update_options_settings_pedido_minimo_tab', __CLASS__ . '::update_settings' );
    }

    public static function add_settings_tab( $settings_tabs ) {
        $settings_tabs['settings_pedido_minimo_tab'] = __( 'WC Pedido Mínimo', 'wc-pedido-minimo' );
        return $settings_tabs;
    }

    public static function update_settings() {
        woocommerce_update_options( self::get_option() );
    }

    public static function settings_tab() {
        woocommerce_admin_fields( self::get_option() );
    }

	public static function get_option() {
            global $woocommerce;
            $simbolo_moeda = get_woocommerce_currency_symbol();
        $settings = [
            'section_title' => array(
                'name'        => __( 'Woocommerce Pedido Mínimo', 'wc-pedido-minimo' ),
                'type'        => 'title',
            ),
            'onoff' => array(
                'name' => __( 'Plugin ativado', 'wc-pedido-minimo' ),
                'type' => 'checkbox',
                'desc' => __( 'Ative ou desative o plugin.', 'wc-pedido-minimo' ),
                'id'   => 'wc-pedido-minimo-onoff',
            ),
            'funcionamento' => array(
                'name'  => __( 'Selecione o modo de funcionamento', 'wc-pedido-minimo' ),
                'type'  => 'select',
                'desc'  => __( 'Escolha se o plugin vai funcionar por valor ou quantidade.', 'wc-pedido-minimo' ),
                'id'    => 'wc-pedido-minimo-funcionamento',
                'class' => 'wc-pedido-minimo-admin-field',
                'options' => array(
                  'valor'       => __( 'Valor', 'wc-pedido-minimo' ),
                  'quantidade'  => __( 'Quantidade', 'wc-pedido-minimo' ),
                ),
            ),
            'valor' => array(
                'name' => __( 'Valor mínimo do pedido em', 'wc-pedido-minimo' ).' '.$simbolo_moeda,
                'type' => 'text',
                'desc' => __( 'Pedidos com valor inferior não serão finalizados.', 'wc-pedido-minimo' ),
                'id'   => 'wc-pedido-minimo-valor',
                'class' => 'wc-pedido-minimo-admin-field',
            ),
            'quantidade' => array(
                'name' => __( 'Quantidade mínima de items no pedido', 'wc-pedido-minimo' ),
                'type' => 'text',
                'desc' => __( 'Pedidos com quantidade de itens inferior não serão finalizados.', 'wc-pedido-minimo' ),
                'id'   => 'wc-pedido-minimo-quantidade',
                'class' => 'wc-pedido-minimo-admin-field',
            ),
            'usuarios' => array(
                'name' => __( 'Selecione uma função de usuário', 'wc-pedido-minimo' ),
                'type' => 'select',
                'desc' => __( 'As regras do plugin serão aplicadas somente para a função de usuário selecionada.<br />Ao selecionar "Nenhuma Função", o plugin irá funcionar para qualquer tipo de usuário.', 'wc-pedido-minimo' ),
                'id'   => 'wc-pedido-minimo-usuarios',
                'class' => 'wc-pedido-minimo-admin-field',
                'options' => wc_pedido_minimo_get_role_names(),
            ),
            'pagamentos[]' => array(
                'name' => __( 'Selecione o(s) método(s) de pagamento', 'wc-pedido-minimo' ),
                'type' => 'multiselect',
                'desc' => __( 'Utilize a tecla CTRL para selecionar múltiplas formas de pagamento.<br />Caso selecionada, essa opção irá fazer com que o valor/quantidade mínima seja aplicada somente com a forma de pagamento selecionada.<br />Essa opção só terá validade na etapa de <strong>CHECKOUT</strong>.', 'wc-pedido-minimo' ),
                'id'   => 'wc-pedido-minimo-pagamentos',
                'class' => 'wc-pedido-minimo-admin-field',
                'options' => wc_pedido_minimo_get_payment_methods(),
            ),
            'section_end' => array(
                 'type' => 'sectionend',
            )
        ];

	    return apply_filters( 'wc_settings_pedido_minimo_tab_settings', $settings );
	}
}
WC_Settings_Pedido_Minimo_Tab::init();


function wc_pedido_minimo_get_role_names() {
    global $wp_roles;
    if ( ! isset( $wp_roles ) )
        $wp_roles = new WP_Roles();
        $select_null = ['' => __('Nenhuma Função', 'wc-pedido-minimo')];
        $all_roles = $wp_roles->get_names();
        $full_array = array_merge($select_null, $all_roles);
    return $full_array;
}


function wc_pedido_minimo_get_payment_methods() {
    $gateways = WC()->payment_gateways->get_available_payment_gateways();
    $enabled_gateways = [];
    if ( isset( $gateways ) )
        $select_null = [0 => __('Nenhum', 'wc-pedido-minimo')];
        foreach ($gateways as $gateway) {
            $enabled_gateways[$gateway->id] .= $gateway->title;
        }
        $full_array = array_merge($select_null, $enabled_gateways);
    return $full_array;
}
