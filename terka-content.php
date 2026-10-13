<?php
/**
 * Plugin Name: Terka – obsah webu a SCF
 * Description: Editovatelný obsah, import původního webu a veřejné API pro Vercel.
 * Requires PHP: 8.0
 * Version: 1.0.0
 */
defined('ABSPATH') || exit;
function terka_schema() {static $schema; if (!$schema) $schema=json_decode(file_get_contents(__DIR__.'/schema.json'),true); return $schema;}
add_action('init',function(){register_post_type('terka_page',['label'=>'Obsah webu Terky','public'=>false,'show_ui'=>true,'supports'=>['title'],'capability_type'=>'page','map_meta_cap'=>true]);});
add_action('acf/init',function(){
 foreach(terka_schema() as $slug=>$page){
  $fields=[];
  foreach($page['fields'] as $f){
   $type=$f['type']==='url'?'text':($f['type']==='heading'?'textarea':$f['type']);
   $field=['key'=>'field_terka_'.$slug.'_'.$f['name'],'name'=>$f['name'],'label'=>$f['label'],'type'=>$type];
   if($type==='image'){$field['return_format']='url';$field['preview_size']='medium';}
   if($type==='wysiwyg'){$field['tabs']='visual';$field['toolbar']='basic';$field['media_upload']=0;}
   if($f['type']==='url')$field['instructions']='Adresa webu, e-mail, telefon nebo interní cesta začínající /.';
   $fields[]=$field;
  }
  $post=get_page_by_path($slug,OBJECT,'terka_page');
  if($post)acf_add_local_field_group(['key'=>'group_terka_'.$slug,'title'=>$slug==='global'?'Hlavička, patička a kontakty':$page['title'],'fields'=>$fields,'location'=>[[['param'=>'post','operator'=>'==','value'=>(string)$post->ID]]]]);
 }
});
function terka_html($slug){
 $schema=terka_schema();if(!isset($schema[$slug]))return '';
 $post=get_page_by_path($slug,OBJECT,'terka_page');$page=$schema[$slug];$html=$page['template'];
 foreach($page['fields'] as $f){
  $v=$post?get_post_meta($post->ID,$f['name'],true):'';
  if($post && !metadata_exists('post',$post->ID,$f['name']))$v=$f['default'];
  if(!$post)$v=$f['default'];
  if($f['type']==='image'){$v=is_numeric($v)?wp_get_attachment_url((int)$v):$v;if(str_starts_with((string)$v,'/assets/'))$v=plugins_url('assets/'.basename($v),__FILE__);}
  $v=$f['type']==='heading'?wp_kses($v,['br'=>[],'strong'=>[],'em'=>[]]):($f['type']==='wysiwyg'?wp_kses_post($v):($f['type']==='url'||$f['type']==='image'?esc_url($v):esc_html($v)));
  $html=str_replace('{{'.$f['name'].'}}',$v,$html);
 }
 // Seed images outside image fields.
 $html=str_replace('src="/assets/','src="'.plugins_url('assets/',__FILE__),$html);
 return $html;
}
function terka_payload($slug){$s=terka_schema();$post=get_page_by_path($slug,OBJECT,'terka_page');$global=explode('</header>',terka_html('global'),2);return ['title'=>$post?$post->post_title:$s[$slug]['title'],'html'=>terka_html($slug),'global'=>['header'=>$global[0].'</header>','footer'=>$global[1]??'']];}
add_action('rest_api_init',function(){register_rest_route('terka/v1','/page/(?P<slug>[a-z0-9-]+)',['methods'=>'GET','permission_callback'=>'__return_true','callback'=>function($r){$slug=$r['slug'];if($slug==='global'||!isset(terka_schema()[$slug]))return new WP_Error('not_found','Stránka neexistuje',['status'=>404]);return rest_ensure_response(terka_payload($slug));}]);});
add_action('admin_menu',function(){add_submenu_page('edit.php?post_type=terka_page','Import webu','Import webu','manage_options','terka-import',function(){
 if(!current_user_can('manage_options'))return;
 if(isset($_POST['terka_import'])){check_admin_referer('terka_import');
  require_once ABSPATH.'wp-admin/includes/file.php';require_once ABSPATH.'wp-admin/includes/media.php';require_once ABSPATH.'wp-admin/includes/image.php';
  foreach(terka_schema() as $slug=>$page){
   if(get_page_by_path($slug,OBJECT,'terka_page'))continue;
   $id=wp_insert_post(['post_type'=>'terka_page','post_status'=>'publish','post_name'=>$slug,'post_title'=>$page['title']??'Hlavička a patička'],true);if(is_wp_error($id))continue;
   foreach($page['fields'] as $f){$v=$f['default'];if($f['type']==='image'&&str_starts_with($v,'/assets/')){
    $name=basename($v);$existing=get_posts(['post_type'=>'attachment','meta_key'=>'_terka_asset','meta_value'=>$name,'fields'=>'ids','posts_per_page'=>1]);
    if($existing)$v=$existing[0];else{$tmp=wp_tempnam($name);copy(__DIR__.'/assets/'.$name,$tmp);$attachment=media_handle_sideload(['name'=>$name,'tmp_name'=>$tmp],$id);if(!is_wp_error($attachment)){update_post_meta($attachment,'_terka_asset',$name);$v=$attachment;}else{@unlink($tmp);}}
   }update_post_meta($id,$f['name'],$v);update_post_meta($id,'_'.$f['name'],'field_terka_'.$slug.'_'.$f['name']);}
  }echo '<div class="notice notice-success"><p>Import dokončen. Existující obsah nebyl přepsán.</p></div>';
 }
 echo '<div class="wrap"><h1>Import webu Terky</h1><p>Vytvoří stránky a fotografie původního webu. Existující stránky nepřepisuje. Nejprve aktivujte Secure Custom Fields.</p><form method="post">';wp_nonce_field('terka_import');submit_button('Importovat výchozí obsah','primary','terka_import');echo '</form></div>';
 });});
add_action('admin_notices',function(){if(!function_exists('acf_add_local_field_group')&&current_user_can('manage_options'))echo '<div class="notice notice-warning"><p>Terka: nainstalujte a aktivujte plugin Secure Custom Fields pro úpravu polí.</p></div>';});
