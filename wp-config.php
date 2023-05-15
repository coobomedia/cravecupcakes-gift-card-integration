<?php

# Database Configuration
define( 'DB_NAME', 'wp_cravecupcakstg' );
define( 'DB_USER', 'cravecupcakstg' );
define( 'DB_PASSWORD', '1Ru-kF2o0Ei1ipPSM5sO' );
define( 'DB_HOST', '127.0.0.1:3306' );
define( 'DB_HOST_SLAVE', '127.0.0.1:3306' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'wp_';

# Security Salts, Keys, Etc
define('AUTH_KEY', '^Kaop+R-4!r^s_t)1#PFqVxXoUYAMJsY=}KPT.7T/iGE-1-.s|T=F,wMr@I%_D1c');
define('SECURE_AUTH_KEY', '?u(&c,,+I=PD8)^1#,dSB07afHo(Y<I+-C*I<4d*W~Bw}<xgs@CJ0p}-kHe(y0:p');
define('LOGGED_IN_KEY', 'ZJvyDKO; !0-2Ga.7?GQGj(-(C0D=VY|^Q_?vm[ZGbdU4Pv7LR,BXp:N/z#K=BLP');
define('NONCE_KEY', ',&7XMAjA3=Q4Y|+CO+vGD7P.j%c5VJ|X.h_R|w)>pJRVRA7erE-dG$;*|llzm  |');
define('AUTH_SALT', 'Y9EAk%StJVa<b$HxlH-G/Y!,r/-N@Hbw#j%/b]|c!<|(q(VUHGi8(Ix$L+@#+k!U');
define('SECURE_AUTH_SALT', 'e1J..|:25@-N+Erp]F9t4IohOH}`[[0|DYA+{{D;5Hv0X7:q6$#(MS_4(w:Yu2BY');
define('LOGGED_IN_SALT', '7xJNKe6cG18$tHu<Ocv-WRw[HXD)y+3K.WIL69|jHRlS9{:iJo}xo;#5eQB^tt0b');
define('NONCE_SALT', 'j~fvfHnp{_;a!OI^I:,|{9#0%w#7r1x,s 5[+ MG 3K!}.hU N>|O]wb,+*;xbdy');


# Localized Language Stuff

define( 'WP_CACHE', TRUE );

define( 'WP_AUTO_UPDATE_CORE', false );

define( 'PWP_NAME', 'cravecupcakstg' );

define( 'FS_METHOD', 'direct' );

define( 'FS_CHMOD_DIR', 0775 );

define( 'FS_CHMOD_FILE', 0664 );

define( 'WPE_APIKEY', '1a73b32f968036ef71c8f8794b0881c05a04f69c' );

define( 'WPE_CLUSTER_ID', '210611' );

define( 'WPE_CLUSTER_TYPE', 'pod' );

define( 'WPE_ISP', true );

define( 'WPE_BPOD', false );

define( 'WPE_RO_FILESYSTEM', false );

define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );

define( 'WPE_SFTP_PORT', 2222 );

define( 'WPE_SFTP_ENDPOINT', '34.136.228.136' );

define( 'WPE_LBMASTER_IP', '' );

define( 'WPE_CDN_DISABLE_ALLOWED', false );

define( 'DISALLOW_FILE_MODS', FALSE );

define( 'DISALLOW_FILE_EDIT', FALSE );

define( 'DISABLE_WP_CRON', false );

define( 'WPE_FORCE_SSL_LOGIN', false );

define( 'FORCE_SSL_LOGIN', false );

/*SSLSTART*/
if (isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL']) {
    $_SERVER['HTTPS'] = 'on';
} /*SSLEND*/

define( 'WPE_EXTERNAL_URL', false );

define( 'WP_POST_REVISIONS', FALSE );

define('WP_DEBUG', false);

define( 'WPE_WHITELABEL', 'wpengine' );

define( 'WP_TURN_OFF_ADMIN_BAR', false );

define( 'WPE_BETA_TESTER', false );

define('WP_MEMORY_LIMIT', '1024M');

umask(0002);

$wpe_cdn_uris=array ( );

$wpe_no_cdn_uris=array ( );

$wpe_content_regexs=array ( );

$wpe_all_domains=array ( 0 => 'cravecupcakstg.wpengine.com', 1 => 'cravecupcakstg.wpenginepowered.com', );

$wpe_varnish_servers=array ( 0 => '127.0.0.1', );

$wpe_special_ips=array ( 0 => '34.68.172.240', 1 => 'pod-210611-utility.pod-210611.svc.cluster.local', );

$wpe_netdna_domains=array ( );

$wpe_netdna_domains_secure=array ( );

$wpe_netdna_push_domains=array ( );

$wpe_domain_mappings=array ( );

$memcached_servers=array ( 'default' =>  array ( 0 => 'unix:///tmp/memcached.sock', ), );

/*SSLSTART*/
if (isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL']) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/

/*SSLSTART*/ if ( isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL'] ) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/
define('WPLANG', '');

# WP Engine ID


# WP Engine Settings


# That's It. Pencils down
if (!defined('ABSPATH')) {
    define('ABSPATH', __DIR__ . '/');
}
require_once(ABSPATH . 'wp-settings.php');
