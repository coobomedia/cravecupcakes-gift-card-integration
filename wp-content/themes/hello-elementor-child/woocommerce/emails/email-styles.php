<?php
/**
 * Email Styles
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/emails/email-styles.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce\Templates\Emails
 * @version 7.4.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

// Load colors.
$bg        = get_option( 'woocommerce_email_background_color' );
$body      = get_option( 'woocommerce_email_body_background_color' );
$base      = get_option( 'woocommerce_email_base_color' );
$base_text = wc_light_or_dark( $base, '#202020', '#ffffff' );
$text      = get_option( 'woocommerce_email_text_color' );

// Pick a contrasting color for links.
$link_color = wc_hex_is_light( $base ) ? $base : $base_text;

if ( wc_hex_is_light( $body ) ) {
	$link_color = wc_hex_is_light( $base ) ? $base_text : $base;
}

$bg_darker_10    = wc_hex_darker( $bg, 10 );
$body_darker_10  = wc_hex_darker( $body, 10 );
$base_lighter_20 = wc_hex_lighter( $base, 20 );
$base_lighter_40 = wc_hex_lighter( $base, 40 );
$text_lighter_20 = wc_hex_lighter( $text, 20 );
$text_lighter_40 = wc_hex_lighter( $text, 40 );

// !important; is a gmail hack to prevent styles being stripped if it doesn't like something.
// body{padding: 0;} ensures proper scale/positioning of the email in the iOS native email app.
?>
    body {
    padding: 0;
    }

    #wrapper {
    background-color: <?php echo esc_attr( $bg ); ?>;
    margin: 0;
    padding: 70px 0;
    -webkit-text-size-adjust: none !important;
    width: 100%;
    }
    #mail_wrapper{
    background-color: <?php echo esc_attr( $bg ); ?>;
    margin: 0;
    padding: 70px 0;
    -webkit-text-size-adjust: none !important;
    width: 100%;
    }

    #template_container {
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1) !important;
    background-color: <?php echo esc_attr( $body ); ?>;
    border: 1px solid <?php echo esc_attr( $bg_darker_10 ); ?>;
    border-radius: 3px !important;
    }
    #email_template_container{
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.1) !important;
    background-color: <?php echo esc_attr( $body ); ?>;
    border:none;
    border-radius: 3px !important;
    border-collapse: collapse;
    }
    #email_template_tbody tr:last-child {
    border-bottom: 2px solid #D8EEE7;
    }
    #p_items div>div{
    position: relative;
    }
    #p_items div>div img{
    position: absolute;
    }
    #email_template_tbody tbody tr {
    border-top: 2px solid #D8EEE7;
    }
    #email_template_tbody tr td div{
    display:flex;
    align-item:center;
    }

    #email_template_section{
    max-width:600px;
    margin:auto;
    background-color: white;
    padding:10px 20px;
    }
    #template_header {
    background-color: <?php echo esc_attr( $base ); ?>;
    border-radius: 3px 3px 0 0 !important;
    color: <?php echo esc_attr( $base_text ); ?>;
    border-bottom: 0;
    font-weight: bold;
    line-height: 100%;
    vertical-align: middle;
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
    }

    #template_header h1,
    #template_header h1 a {
    color: <?php echo esc_attr( $base_text ); ?>;
    background-color: inherit;
    }

    #template_header_image img {
    margin-left: 0;
    margin-right: 0;
    }

    #template_footer td {
    padding: 0;
    border-radius: 6px;
    }

    #template_footer #credit {
    border: 0;
    color: <?php echo esc_attr( $text_lighter_40 ); ?>;
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
    font-size: 12px;
    line-height: 150%;
    text-align: center;
    padding: 24px 0;
    }

    #template_footer #credit p {
    margin: 0 0 16px;
    }

    #body_content {
    background-color: <?php echo esc_attr( $body ); ?>;
    }

    #body_content table td {
    padding: 48px 48px 32px;
    }

    #body_content table td td {
    padding: 12px;
    }

    #body_content table td th {
    padding: 12px;
    }

    #body_content td ul.wc-item-meta {
    font-size: small;
    margin: 1em 0 0;
    padding: 0;
    list-style: none;
    }

    #body_content td ul.wc-item-meta li {
    margin: 0.5em 0 0;
    padding: 0;
    }

    #body_content td ul.wc-item-meta li p {
    margin: 0;
    }

    #body_content p {
    margin: 0 0 16px;
    }

    #body_content_inner {
    color: <?php echo esc_attr( $text_lighter_20 ); ?>;
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
    font-size: 14px;
    line-height: 150%;
    text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
    }

    .td {
    color: <?php echo esc_attr( $text_lighter_20 ); ?>;
    border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
    vertical-align: middle;
    }

    .address {
    padding: 12px;
    color: <?php echo esc_attr( $text_lighter_20 ); ?>;
    border: 1px solid <?php echo esc_attr( $body_darker_10 ); ?>;
    }

    .text {
    color: <?php echo esc_attr( $text ); ?>;
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
    }

    .link {
    color: <?php echo esc_attr( $link_color ); ?>;
    }

    #header_wrapper {
    padding: 36px 48px;
    display: block;
    }

    h1 {
    color: <?php echo esc_attr( $base ); ?>;
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
    font-size: 30px;
    font-weight: 300;
    line-height: 150%;
    margin: 0;
    text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
    text-shadow: 0 1px 0 <?php echo esc_attr( $base_lighter_20 ); ?>;
    }

    h2 {
    color: <?php echo esc_attr( $base ); ?>;
    display: block;
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
    font-size: 18px;
    font-weight: bold;
    line-height: 130%;
    margin: 0 0 18px;
    text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
    }

    h3 {
    color: <?php echo esc_attr( $base ); ?>;
    display: block;
    font-family: "Helvetica Neue", Helvetica, Roboto, Arial, sans-serif;
    font-size: 16px;
    font-weight: bold;
    line-height: 130%;
    margin: 16px 0 8px;
    text-align: <?php echo is_rtl() ? 'right' : 'left'; ?>;
    }

    a {
    color: <?php echo esc_attr( $link_color ); ?>;
    font-weight: normal;
    text-decoration: underline;
    }

    img {
    border: none;
    display: inline-block;
    font-size: 14px;
    font-weight: bold;
    height: auto;
    outline: none;
    text-decoration: none;
    text-transform: capitalize;
    vertical-align: middle;
    max-width: 100%;

    }
    #topp img{

    }
    #thankyou-detail h2 {
    text-align: center;
    color: #55B7B3;
    }
    #order-information h3 {
    padding: 15px 0;
    margin: 0 !important;
    font-size: 18px !important;
    }
    #mini-cupcake h3, #billing-info h3, #ship-info h3 {
    padding: 25px 0 0 0;
    margin: 0 !important;
    font-size: 18px !important;
    }
    #order-information-detail {
    padding: 0;
    }
    #mini-cup-cakes-detail tr > th {
    border: none;
    color: #55B7B3;
    font-family: "Montserrat", Sans-serif;
    }

    #mini-cup-cakes-detail table thead:first-child tr:first-child th {
    border: none;
    padding: 8px 0;
    }

    #mini-cup-cakes-detail table thead:first-child tr th:first-child {
    text-align: left;
    }

    #mini-cup-cakes-detail table th:last-child,
    #mini-cup-cakes-detail table td:last-child {
    text-align: right !important;
    }
    #item-detail {
    display: flex;
    align-items: center;
    justify-content: flex-start;
    }

    #item-detail img {
    width: 40px;

    }
    #packaging-detail span {
    text-transform: capitalize;
    }
    #mini-cup-cakes-detail tbody td {
    padding: 10px;
    }

    #mini-cup-cakes-detail tbody tr {
    border-top: 2px solid #D8EEE7;
    }
    #mini-cup-cakes-detail tbody tr:last-child {
    border-bottom: 2px solid #D8EEE7;
    }

    #mini-cup-cakes-detail tbody tr td {
    border: none;
    background: transparent;
    }

    #mini-cup-cakes-detail tbody tr td:not(:first-child) {
    text-align: center;
    }

    #mini-cup-cakes-detail table tbody > tr:nth-child(odd) > td, table tbody > tr:nth-child(odd) > th {
    background: transparent;
    }

    #mini-cup-cakes-detail table td {
    vertical-align: middle;
    font-family: "Montserrat", Sans-serif;
    font-weight: 700;
    color: black;
    }
    .billing-info p:not(:last-child), .ship-info p:not(:last-child) {
    margin-bottom: 0;
    line-height: 20px;
    }

    .billing-info h3, .ship-info h3 {
    padding: 25px 0 10px 0;
    }

    .item-detail span {
    padding-left: 20px;
    }
    .packaging-item .item {
    width: 50px;
    height: 50px;
    position: relative;
    }

    #packaging-item {
    display: inline-block;
    }

<?php
