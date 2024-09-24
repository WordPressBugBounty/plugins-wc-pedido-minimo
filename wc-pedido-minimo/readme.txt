=== WooCommerce Pedido Mínimo ===
Contributors: art2web
Donate link: http://art2web.com.br/doacoes
Tags: woocommerce, pedido minimo, pedido mínimo, minimum order, plugin woocommerce
Requires at least: 4.0
Tested up to: 5.5.1
Stable tag: 2.1.0
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Plugin para configurar valor mínimo ou quantidade mínima de items para finalização de pedidos no WooCommerce. O plugin também permite selecionar uma função de usuário do Wordpress para aplicar as regras configuradas.

== Description ==

Defina um valor mínimo ou quantidade mínima de itens para permitir a finalização de pedidos no WooCommerce.

Caso o cliente tente finalizar a compra sem atingir o valor mínimo ou quantidade mínima de itens, será exibida mensagem informando o valor/quantidade mínimo(a), o valor/item(ns) total do pedido e o valor/item(ns) necessário(s) para atingir o mínimo configurado para a loja.
O plugin também permite selecionar uma função de usuário do Wordpress para aplicar as regras do plugin.

* É necessário que o WooCommerce esteja instalado e ativo para utilizar este plugin.

The plugin has translations for:

- Portuguese (Brazil)
- English
- Spanish (Spain, Argentina and Colombia)

Description: Plugin to set minimum value for order checkout in WooCommerce.

Descripción: Plugin para establecer el valor mínimo para el pago del pedido en WooCommerce.


== Installation ==

= Instalação do plugin: =

- Faça o upload da pasta wc-pedido-minimo para a pasta de plugins ou instale diretamente através da sua instalação do Wordpress .
- Ative o plugin.


= Configuração do plugin: =

- Acesse WooCommerce->Configurações e acesse a guia "WC Pedido Mínimo".

- Acesse "WooCommerce->Configurações" e clique na guia "WC Pedido Mínimo".

- Ative o plugin.

- Selecione se vai utilizar o funcionamento por valor ou quantidade.

- Preencha o campo com o valor ou quantidade mínima desejada.

- O plugin pode ser aplicado a uma determinada função de usuário do Wordpress. Se for o caso, selecione um ou deixe em "Nenhuma Função".

- Clique em "Salvar Alterações".


== Frequently Asked Questions ==

= Qual é a licença do plugin? =
Este plugin possui licença GPL.

= Quais os requisitos necessários para utilizar o plugin? =
* WooCommerce 3.0 ou posterior.

= O plugin leva em consideração o valor do frete? =
* Não. O cálculo só leva em consideração os produtos inseridos no carrinho de compras.


== Screenshots ==

1. Tela de configuração do plugin.

2. Exemplo de pedido mínimo por valor.

3. Exemplo de pedido mínimo por quantidade.


== Changelog ==

= 1.0.0 - 2018/01/09 =

- Lançamento do plugin.

= 1.2.0 - 2018/01/14 =

- Tradução Português/Inglês.
- Alteração no preenchimento do campo de valor no admin. Agora sem máscara e sem símbolo de moeda, utilizando mesmo padrão do campo de valores (como preço de produtos) no WooCommerce.
- A formatação de preços e símbolo da moeda serão exibidos de acordo com o padrão configurado na loja.

= 1.2.1 - 2018/08/11 =

- Tradução Espanhol.
- Correção da condição para pedido mínimo em load-pedido-minimo.php.

= 2.0.0 - 2018/11/23 =

- Opção para ativar/desativar plugin diretamente na tela de configuração.
- Seleção de valor mínimo ou quantidade mínima de itens no pedido.
- Possibilidade de escolher uma função de usuário para a regra (valor/quantidade) ser aplicada.

= 2.0.1 - 2018/11/23 =

- Correção dos arquivos de tradução e textos em espanhol.

= 2.0.2 - 2019/10/01 =

- Ajuste para que o CSS personalizado do plugin não interfira em outras páginas de configurações do Woocommerce.
Contribuição do @myst1010

= 2.0.3 - 2020/06/27 =

- Ajustes nas traduções para espanhol e inglês.
- Termos das opções do painel administrativo adicionados na tradução

= 2.0.4 - 2020/06/29 =

- Ajustes no caminho para as traduções.

= 2.1.0 =

- Adicionada a função de selecionar método de pagamento requerido para finalização do pedido.
É uma regra adicional que é validada na etapa de Checkout.

== Upgrade Notice ==

= 2.1.1 =
