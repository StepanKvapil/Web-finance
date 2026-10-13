<?php
defined('ABSPATH') || exit;
if(!function_exists('terka_payload'))wp_die('Aktivujte plugin Terka – obsah webu a SCF.');
$slug=trim(parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH),'/');$base=trim(parse_url(home_url('/'),PHP_URL_PATH),'/');if($base)$slug=trim(substr($slug,strlen($base)),'/');$slug=$slug?:'home';
if(!isset(terka_schema()[$slug])||$slug==='global'){status_header(404);$data=['title'=>'Stránka nenalezena','html'=>'<section class="page-intro"><h1>Stránka nenalezena</h1><a href="/">Zpět na úvod</a></section>','global'=>terka_payload('home')['global']];}else{$data=terka_payload($slug);status_header(200);}
function terka_local_links($html){return preg_replace_callback('~href="(/[^\"]*)"~',function($m){return 'href="'.esc_url(home_url($m[1])).'"';},$html);}
?><!doctype html><html <?php language_attributes(); ?>><head><meta charset="<?php bloginfo('charset'); ?>"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?php echo esc_html($data['title']); ?></title><?php wp_head(); ?></head><body><a class="skip" href="#obsah">Přejít na obsah</a><div class="site-shell"><?php echo terka_local_links($data['global']['header']); ?><main id="obsah"><?php echo terka_local_links($data['html']); ?></main><?php echo terka_local_links($data['global']['footer']); ?></div><?php wp_footer(); ?></body></html>
