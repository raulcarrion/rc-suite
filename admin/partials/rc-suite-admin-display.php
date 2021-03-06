<?php

/**
 * @link       https://www.raulcarrion.com/
 * @since      1.0.0
 *
 * @package    Rc_Suite
 * @subpackage Rc_Suite/admin/partials
 */

//$reemplazador = new Rc_Suite_Reemplazador();

$reemplazador = new Rc_Suite_Reemplazador(unserialize(get_option("rc-suite-reemplazos","")));

/*
		GESTION DE PETICIONES
*/

/*
				mensajes
*/

if (isset($_GET['rcsu-status']))
{
	switch ($_GET['rcsu-status']) 
	{ 
		case 'general':
			switch ($_GET['rcsu-msg']){
				case 'tab':
					add_settings_error('general', 'message-comments',__("Unknow origin","rc-suite"), 'error');
					break;
				default:
					add_settings_error('general', 'message-comments',__("Generic error","rc-suite"), 'error');
				break;
			}
			break;
		case 'ok_file_upload':
			add_settings_error('file_upload', 'ok', __("File uploaded successfuly","rc-suite"), 'success');
			break;
		case 'ko_file_upload':
			add_settings_error('file_upload', 'ko', __("There was an error uploading the file!","rc-suite"), 'error');
			break;
		case 'ok_file_test':
			add_settings_error('file_test', 'ok', __("Mathes using file ".$_GET['rcsu-file'].": " . $_GET['rcsu-msg'],"rc-suite"), 'success');
			break;
		case 'ok_file_del':
			add_settings_error('file_del', 'ok', __("File ".$_GET['rcsu-file']." successfully deleted","rc-suite"), 'success');
			break;
		
		case 'ko_file_del':
			add_settings_error('file_del', 'ko', __("Error deleting the file ".$_GET['rcsu-file'].".","rc-suite"), 'error');
			break;
							
		case 'ok_file_process':
			add_settings_error('file_process', 'ok', __("Number of replacements done using file ".$_GET['rcsu-file'].": " . $_GET['rcsu-msg'],"rc-suite"), 'success');
			break;
		case 'ko_file_process':
			add_settings_error('file_process', 'ko', __("There was an error processing file ".$_GET['rcsu-file'],"rc-suite"), 'error');
			break;
				
		case 'ok_get_replacements':
			add_settings_error('get_replacements', 'ok', __("Matches found: " . $_GET['rcsu-msg'],"rc-suite"), 'success');
			break;
		case 'ok_replacements':
			add_settings_error('replacements', 'ok', __("Number of replacements done: " . $_GET['rcsu-msg'],"rc-suite"), 'success');
			break;
	
		default:
			add_settings_error('default', 'ko', __("Unrecognized message","rc-suite"). $_GET['rcsu-status'], 'error');
			break;
	}
}

/*
			Reemplazador - subir fichero
*/
if (isset($_POST['rcsu-upload-file-button']))
{
	if ( ! isset( $_POST['rcsu-suite-nonce'] ) || ! wp_verify_nonce( $_POST['rcsu-suite-nonce'], 'replacer_upload_file' ) )
  	{
		wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&rcsu-status=&rcsu-msg=nonce'));
		exit;
	}
	else 
	{
		$fichero_subido = PLUGIN_PATH_UPLOAD_FILES . basename($_FILES['rcsu-upload-file']['name']);

		if (move_uploaded_file($_FILES['rcsu-upload-file']['tmp_name'], $fichero_subido)) 
			wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&tab=reemplazador&rcsu-status=ok_file_upload&rcsu-file='. $_FILES['rcsu-upload-file']['name']));
		else 
			wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&tab=reemplazador&rcsu-status=ko_file_upload'));
	}
}

/*
			Reemplazador - borrar fichero
*/
if (isset($_POST['rcsu-delete-file']))
{
	if ( ! isset( $_POST['rcsu-suite-nonce'] ) || ! wp_verify_nonce( $_POST['rcsu-suite-nonce'], 'replacer_process_file' ) )
		wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&tab=reemplazador&rcsu-status=&rcsu-msg=nonce'));
	else 
	{
		if (!empty($_POST['rcsu-choose-file']))
		{
			//Borramos el fichero
			if (unlink(PLUGIN_PATH_UPLOAD_FILES.$_POST['rcsu-choose-file']))
				wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&tab=reemplazador&rcsu-status=ok_file_del&rcsu-file='. $_POST['rcsu-choose-file']));
			else
				wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&tab=reemplazador&rcsu-status=ko_file_del&rcsu-msg=no-file&rcsu-file='. $_POST['rcsu-choose-file']));
		}
		else
			wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&tab=reemplazador&rcsu-status=ko_file_del&rcsu-msg=no-file&rcsu-file='. $_POST['rcsu-choose-file']));
	}
	exit();
}

/*	
			Reemplazador - numero de hits manual
*/
if (isset($_POST['rcsu-get-number-replaces']))
{
	if ( ! isset( $_POST['rcsu-suite-nonce'] ) || ! wp_verify_nonce( $_POST['rcsu-suite-nonce'], 'replacer_manual' ) )
  	{
		wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&rcsu-status=&rcsu-msg=nonce'));
		exit;
	}
	else
	{
		if (!empty($_POST['rcsu-source-string'] ) && !empty($_POST['rcsu-field-name'] ) && !empty($_POST['rcsu-table-name'] )	)
		{
			$reemplazos = array();
			// Preparamos los datos
			$tabla 		= str_replace("\\\\", "\\" , $_POST['rcsu-table-name']);
			$campo 	 	= str_replace("\\\\", "\\" , $_POST['rcsu-field-name']);
			$origen 	= str_replace("\\\\", "\\" , $_POST['rcsu-source-string']);
			$destino 	= isset($_POST['rcsu-destination-string']) ? str_replace("\\\\", "\\" , $_POST['rcsu-destination-string']) : "";
			
			array_push($reemplazos, array($tabla => (array( $campo , $origen, $destino))));

			//Guardamos los datos usados para mostrar la pagina
			update_option("rc-suite-reemplazos", serialize($reemplazos));			
			
			$reemplazador = new Rc_Suite_Reemplazador($reemplazos);
			$a_reemplazar = $reemplazador->get_num_reemplazos();
			
			if ($a_reemplazar !== FALSE)
				wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&tab=reemplazador&rcsu-status=ok_get_replacements&rcsu-msg='.$a_reemplazar[0]['Coincidencias']));
			else
				wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&tab=reemplazador&rcsu-status=ko_get_replacements&rcsu-msg='));
						
		}
		else 
		{
			wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&tab=reemplazador&rcsu-status=&rcsu-msg=empty_field'));
		}
	}
	exit();
}

/*	
			Reemplazador - reemplazar manual
*/
if (isset($_POST['rcsu-replace']))
{
	if ( ! isset( $_POST['rcsu-suite-nonce'] ) || ! wp_verify_nonce( $_POST['rcsu-suite-nonce'], 'replacer_manual' ) )
  	{
		wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&rcsu-status=&rcsu-msg=nonce'));
		exit;
	}
	else
	{
		if (!empty($_POST['rcsu-source-string'] ) && !empty($_POST['rcsu-field-name'] ) && !empty($_POST['rcsu-table-name'] )	)
		{
			$reemplazos = array();

			// Preparamos los datos
			$tabla 		= str_replace("\\\\", "\\" , $_POST['rcsu-table-name']);
			$campo 	 	= str_replace("\\\\", "\\" , $_POST['rcsu-field-name']);
			$origen 	= str_replace("\\\\", "\\" , $_POST['rcsu-source-string']);
			$destino 	= isset($_POST['rcsu-destination-string']) ? str_replace("\\\\", "\\" , $_POST['rcsu-destination-string']) : "";

			array_push($reemplazos, array($tabla => (array( $campo , $origen, $destino))));

			//Guardamos los datos usados para mostrar la pagina
			update_option("rc-suite-reemplazos", serialize($reemplazos));
								
			$reemplazador = new Rc_Suite_Reemplazador($reemplazos);
			$a_reemplazar = $reemplazador->do_replaces();
			
			
			if ($a_reemplazar !== FALSE)
			wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&tab=reemplazador&rcsu-status=ok_replacements&rcsu-msg='.$a_reemplazar[0]['Afectadas']));
			else
				wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&tab=reemplazador&rcsu-status=ko_replacements&rcsu-msg='));
			
		}
		else 
		{
			wp_safe_redirect(get_home_url(get_current_blog_id() , '/wp-admin/admin.php?page=rc-suite&tab=reemplazador&rcsu-status=&rcsu-msg=empty_field'));
		}
	}
	exit();
}


/*
	Pagina principal de RC-SUITE
*/

function rc_suite_admin_page(){
	global $sd_active_tab;

	$sd_active_tab = isset( $_GET['tab'] ) ? $_GET['tab'] : 'general'; 
	?>

	<h1>RC Suite</h1>
		<p><i><?php _e("Configura el plugin de moda.","rc-suite");?></i></p>
	
	<h2 class="nav-tab-wrapper">
	
	<?php
		do_action( 'rc_suite_settings_tab' );
	?>
	</h2>
		<?php
	do_action( 'rc_suite_settings_content' );

}

/*
* 	Pestaña GENERAL
*/

add_action( 'rc_suite_settings_tab', 'rc_suite_general', 1 );

function rc_suite_general(){
	global $sd_active_tab;
	?>
		<a class="nav-tab <?php echo $sd_active_tab == 'general' || '' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=rc-suite&tab=general' ); ?>"><?php _e("General","rc-suite");?></a>
	<?php
}

/*
* 	Contenido
*/

add_action( 'rc_suite_settings_content', 'rc_suite_general_content' );

function rc_suite_general_content() {
	global $sd_active_tab;
	if ( '' || 'general' != $sd_active_tab )
		return;
?>
    <div class="wrap">
	  <h2>Funcionalidades</h2>
	  <form method="post" action="options.php">
	     <?php wp_nonce_field('update-options') ?>
	     <table class="form-table">
	     	<tbody>
	     		<tr valign="top">
	     			<th scope="row" class="titledesc">CSS para child themes</th>
	     			<td class="forminp forminp-checkbox">
	     				<fieldset>
	     					<legend class="screen-reader-text">
	     						<span>CSS para child themes</span>
	     					</legend>
	     					<label for="rc_parent_css_enabled">
	     						<input type="checkbox" name="rc_parent_css_enabled" id="rc_parent_css_enabled" value="1" <?php if(get_option('rc_parent_css_enabled')==1){ ?>checked="checked" <?php } ?>>
	     						<strong>Activar</strong>
	     					</label>
	     					<p class="description">Enlaza el CSS del tema padre cuando trabajamos con un tema hijo.</p>
	     				</fieldset>
	     			</td>
	     		</tr>
	     		<tr valign="top">
	     			<th scope="row" class="titledesc">Anticaché CSS</th>
	     			<td class="forminp forminp-checkbox">
	     				<fieldset>
	     					<legend class="screen-reader-text">
	     						<span>Anticaché CSS</span>
	     					</legend>
	     					<label for="rc_anti_cache_css_enabled">
	     						<input type="checkbox" name="rc_anti_cache_css_enabled" id="rc_anti_cache_css_enabled" value="1" <?php if(get_option('rc_anti_cache_css_enabled')==1){ ?>checked="checked" <?php } ?>>
	     						<strong>Activar</strong>
	     					</label>
	     					<p class="description">Añade a los archivos de estilos un número de versión aleatorio para evitar la caché mientras trabajamos.</p>
	     				</fieldset>
	     			</td>
	     		</tr>	
	     		<tr valign="top">
	     			<th scope="row" class="titledesc">Anti publi de plugins</th>
	     			<td class="forminp forminp-checkbox">
	     				<fieldset>
	     					<legend class="screen-reader-text">
	     						<span>Anti publi de plugins</span>
	     					</legend>
	     					<label for="rc_anti_publi_plugins_enabled">
	     						<input type="checkbox" name="rc_anti_publi_plugins_enabled" id="rc_anti_publi_plugins_enabled" value="1" <?php if(get_option('rc_anti_publi_plugins_enabled')==1){ ?>checked="checked" <?php } ?>>
	     						<strong>Activar</strong>
	     					</label>
	     					<p class="description">Oculta los mensajes de publicidad, actualizaciones y demás que se muestran en la parte superior del área de administración.</p>
	     				</fieldset>
	     			</td>
	     		</tr>
	     		<tr valign="top">
	     			<th scope="row" class="titledesc">Acceso clientes para WooCommerce</th>
	     			<td class="forminp forminp-checkbox">
	     				<fieldset>
	     					<legend class="screen-reader-text">
	     						<span>Acceso clientes para WooCommerce</span>
	     					</legend>
	     					<label for="rc_login_customer_enabled">
	     						<input type="checkbox" name="rc_login_customer_enabled" id="rc_login_customer_enabled" value="1" <?php if(get_option('rc_login_customer_enabled')==1){ ?>checked="checked" <?php } ?>>
	     						<strong>Activar</strong>
	     					</label>
	     					<p class="description">Añade en el menú secundario el acceso al área de clientes, mostrando el nombre de usuario cuando está logueado.</p>
	     				</fieldset>
	     			</td>
	     		</tr>
	     		<tr valign="top">
	     			<th scope="row" class="titledesc">Desactivar los proyectos en DIVI</th>
	     			<td class="forminp forminp-checkbox">
	     				<fieldset>
	     					<legend class="screen-reader-text">
	     						<span>Desactivar los proyectos en DIVI</span>
	     					</legend>
	     					<label for="rc_divi_projects_disabled_enabled">
	     						<input type="checkbox" name="rc_divi_projects_disabled_enabled" id="rc_divi_projects_disabled_enabled" value="1" <?php if(get_option('rc_divi_projects_disabled_enabled')==1){ ?>checked="checked" <?php } ?>>
	     						<strong>Activar</strong>
	     					</label>
	     					<p class="description">Desactiva el tipo de dato "Proyectos" que se genera automática al activar el tema DIVI.</p>
	     				</fieldset>
	     			</td>
				</tr>
				<!-- HIDE PRICE RANGE	!-->
				<tr valign="top">
	     			<th scope="row" class="titledesc"><?php _e("Hide Price Range for WooCommerce Variable Products","rc-suite") ?></th>
	     			<td class="forminp forminp-checkbox">
	     				<fieldset>
	     					<legend class="screen-reader-text">
	     						<span><?php _e("Hide Price Range for WooCommerce Variable Products","rc-suite") ?>
	     					</legend>
	     					<label for="rcsu_woo_hide_price_range_enabled">
	     						<input type="checkbox" name="rcsu_woo_hide_price_range_enabled" id="rcsu_woo_hide_price_range_enabled" value="1" <?php if(get_option('rcsu_woo_hide_price_range_enabled')){ ?>checked="checked" <?php } ?>>
	     						<strong><?php _e("Activate","rc-suite") ?></strong>
	     					</label>
	     					<p class="description"><?php _e("Hide Price Range for WooCommerce Variable Products","rc-suite"); ?></p>
	     				</fieldset>
	     			</td>
				</tr>
				<!-- MEGAMENU MOBILE COLAPSABLE	!-->
				<tr valign="top">
	     			<th scope="row" class="titledesc"><?php _e("Collapsable mobile menu","rc-suite") ?></th>
	     			<td class="forminp forminp-checkbox">
	     				<fieldset>
	     					<legend class="screen-reader-text">
	     						<span><?php _e("Collapsable mobile menu","rc-suite") ?>
	     					</legend>
	     					<label for="rcsu_collapsable_megamenu_enabled">
	     						<input type="checkbox" name="rcsu_collapsable_megamenu_enabled" id="rcsu_collapsable_megamenu_enabled" value="1" <?php if(get_option('rcsu_collapsable_megamenu_enabled')){ ?>checked="checked" <?php } ?>>
	     						<strong><?php _e("Activate","rc-suite") ?></strong>
	     					</label>
	     					<p class="description"><?php _e("Use collapsable menu in mobile view","rc-suite"); ?></p>
	     				</fieldset>
	     			</td>
				</tr>
				<!-- 4_2 Product Columns 	!-->
				<tr valign="top">
	     			<th scope="row" class="titledesc"><?php _e("4/2 Product columns on desktop/mobile","rc-suite") ?></th>
	     			<td class="forminp forminp-checkbox">
	     				<fieldset>
	     					<legend class="screen-reader-text">
	     						<span><?php _e("4/2 Product columns on desktop/mobile","rc-suite") ?>
	     					</legend>
	     					<label for="rcsu_4_2_columns_enabled">
	     						<input type="checkbox" name="rcsu_4_2_columns_enabled" id="rcsu_4_2_columns_enabled" value="1" <?php if(get_option('rcsu_4_2_columns_enabled')){ ?>checked="checked" <?php } ?>>
	     						<strong><?php _e("Activate","rc-suite") ?></strong>
	     					</label>
	     					<p class="description"><?php _e("4 and 2 products colums view on desktop and mobile","rc-suite"); ?></p>
	     				</fieldset>
	     			</td>
	     		</tr>
				<!-- MOBILE MENU CENTERED 	!-->
				<tr valign="top">
	     			<th scope="row" class="titledesc"><?php _e("Enable search on mobile for Divi centered","rc-suite") ?></th>
	     			<td class="forminp forminp-checkbox">
	     				<fieldset>
	     					<legend class="screen-reader-text">
	     						<span><?php _e("4/2 Product columns on desktop/mobile","rc-suite") ?>
	     					</legend>
	     					<label for="rcsu_mobile_search_centered_enabled">
	     						<input type="checkbox" name="rcsu_mobile_search_centered_enabled" id="rcsu_mobile_search_centered_enabled" value="1" <?php if(get_option('rcsu_mobile_search_centered_enabled')){ ?>checked="checked" <?php } ?>>
	     						<strong><?php _e("Activate","rc-suite") ?></strong>
	     					</label>
	     					<p class="description"><?php _e("Enable search on mobile for Divi centered and centered inline Logo headers. going to Divi -> Theme Customizer -> Header & Navigation -> Header Elements and check the “Show search icon” checkbox","rc-suite"); ?></p>
	     				</fieldset>
	     			</td>
	     		</tr>
	     	</tbody>
	     </table>
	     <p class="submit">
	     	<button name="save" class="button-primary" type="submit" value="Guardar los cambios">Guardar los cambios</button>
	     </p>
	     <input type="hidden" name="action" value="update" />
	     <input type="hidden" name="page_options" value="rc_anti_cache_css_enabled,rc_anti_publi_plugins_enabled,rc_login_customer_enabled, rc_parent_css_enabled,rc_divi_projects_disabled_enabled,rcsu_woo_hide_price_range_enabled,rcsu_collapsable_megamenu_enabled,rcsu_4_2_columns_enabled, rcsu_mobile_search_centered_enabled" />
	  </form>
	</div>
<?php
}

/*
* 	Pestaña SEO
*/

add_action( 'rc_suite_settings_tab', 'rc_suite_seo', 1 );

function rc_suite_seo(){
	global $sd_active_tab;
	?>
		<a class="nav-tab <?php echo $sd_active_tab == 'seo' || '' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=rc-suite&tab=seo' ); ?>"><?php _e("Seo","rc-suite");?></a>
	<?php
}

/*
* 	Contenido
*/

add_action( 'rc_suite_settings_content', 'rc_suite_seo_content' );

function rc_suite_seo_content() {
	global $sd_active_tab;
	if ( '' || 'seo' != $sd_active_tab )
		return;
?>
    <div class="wrap">
	  <h2><?php _e("Seo", "rc-suite") ?></h2>
	  <form method="post" action="options.php">
	     <?php wp_nonce_field('update-options') ?>
	     <table class="form-table">
	     	<tbody>
	     		<tr valign="top">
	     			<th scope="row" class="titledesc"><?php _e("Blog post's under /blog/ address","rc-suite") ?></th>
	     			<td class="forminp forminp-checkbox">
	     				<fieldset>
	     					<legend class="screen-reader-text">
	     						<span><?php _e("Force blog post's under /blog/ address","rc-suite") ?></span>
	     					</legend>
	     					<label for="rcsu_seo_blog_enabled">
	     						<input type="checkbox" name="rcsu_seo_blog_enabled" id="rcsu_seo_blog_enabled" value="1" <?php if(get_option('rcsu_seo_blog_enabled')==1){ ?>checked="checked" <?php } ?>>
	     						<strong><?php _e("Activate","rc-suite") ?></strong>
	     					</label>
	     					<p class="description"><?php _e("Force blog post's address to appear under /blog/ address","rc-suite") ?></p>
	     				</fieldset>
	     			</td>
	     		</tr>
	     	</tbody>
	     </table>
	     <p class="submit">
	     	<button name="save" class="button-primary" type="submit" value="Guardar los cambios"><?php _e("Save changes","rc-suite") ?></button>
	     </p>
	     <input type="hidden" name="action" value="update" />
	     <input type="hidden" name="page_options" value="rcsu_seo_blog_enabled" />
	  </form>
	</div>
<?php
}


/*
* 	Pestaña REEMPLAZADOR
*/

add_action( 'rc_suite_settings_tab', 'rc_suite_reemplazador', 1 );

function rc_suite_reemplazador(){
	global $sd_active_tab;
	?>
		<a class="nav-tab <?php echo $sd_active_tab == 'reemplazador' || '' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=rc-suite&tab=reemplazador' ); ?>"><?php _e("Replacer","rc-suite");?></a>
	<?php
}

/*
* 	Contenido
*/

add_action( 'rc_suite_settings_content', 'rc_suite_reemplazador_content' );

function rc_suite_reemplazador_content() {
	global $sd_active_tab;
	global $wpdb;
	
	// Obtenemos los datos del ultimo reemplazador
	$reemplazador = new Rc_Suite_Reemplazador(unserialize(get_option("rc-suite-reemplazos", "")));

	if ( '' || 'reemplazador' != $sd_active_tab )
		return;
?>
    <div class="wrap">
	  <h2><?php _e("Replacer", "rc-suite"); ?></h2>
	  <h3><?php _e("Manual", "rc-suite"); ?></h3>	 
	  <form method="post" action="<?= get_home_url(get_current_blog_id()) . $_SERVER['PHP_SELF']?>">
	  <tr valign="top">
	  	<table class="form-table">
			<tbody>

				<?php 	// Se añade un campo nonce para probarlo más adelante cuando validemos
						wp_nonce_field( 'replacer_manual', 'rcsu-suite-nonce' ); 
				?>
				<tr>
					<th scope="row" class="titledesc">
						<label id="label_rcsu-prefix" for="rcsu-prefix"><?php _e("Prefix","rc-suite");?></label>
					</th>
					<td class="forminp forminp-select">
						<input type="text" id="rcsu-prefix" name="rcsu-prefix" disabled value="<?php echo $wpdb->prefix ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row" class="titledesc">
						<label id="label_rcsu-table-name" for="rcsu-table-name"><?php _e("Table name","rc-suite");?></label>
					</th>
					<td class="forminp forminp-select">
						<input type="text" id="rcsu-table-name" name="rcsu-table-name" value="<?php echo is_null($reemplazador) ? "" : $reemplazador->get_table(0) ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row" class="titledesc">
						<label id="label_rcsu-field-name" for="rcsu-field-name"><?php _e("Field name","rc-suite");?></label>
					</th>
					<td class="forminp forminp-select">
						<input type="text" id="rcsu-field-name" name="rcsu-field-name" value="<?php echo is_null($reemplazador) ? "" : $reemplazador->get_field(0) ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row" class="titledesc">
						<label id="label_rcsu-source-string" for="rcsu-source-string"><?php _e("Source string","rc-suite");?></label>
					</th>
					<td class="forminp forminp-select">
						<input type="text" id="rcsu-source-string" name="rcsu-source-string" value="<?php echo is_null($reemplazador) ? "" : $reemplazador->get_source(0) ?>" />
					</td>
				</tr>
				<tr>
					<th scope="row" class="titledesc">
						<label id="label_rcsu-destination-string" for="rcsu-destination-string"><?php _e("Replacement string","rc-suite");?></label>
					</th>
					<td class="forminp forminp-select">
						<input type="text" id="rcsu-destination-string" name="rcsu-destination-string" value="<?php echo is_null($reemplazador) ? "" : $reemplazador->get_destination(0) ?>" />
					</td>
				</tr>
				</tr>
				<tr>
					<th></th>
					<td>
						<p class="submit">
						<button id="rcsu-get-number-replaces" name="rcsu-get-number-replaces" class="button-primary" type="submit" value="rc-suite-get-replaces"><?php _e("Test","rc-suite");?></button>
						<button id="rcsu-replace" name="rcsu-replace" class="button-primary" type="submit" value="rc-suite-replace"><?php _e("Replace","rc-suite");?></button>
					</p>	
					</td>
				</tr>
			</tbody>
			</table>
			
	  </form>
	  <h3><?php _e("By file", "rc-suite"); ?></h3>	 
	  
	  <form method="post" action="<?= get_home_url(get_current_blog_id()) . $_SERVER['PHP_SELF']?>" enctype="multipart/form-data">
	  <tr valign="top">
	  	<table class="form-table">
			<tbody>
				<?php 	// Se añade un campo nonce para probarlo más adelante cuando validemos
						wp_nonce_field( 'replacer_upload_file', 'rcsu-suite-nonce' ); 
				?>
				<tr>
					<th scope="row" class="titledesc">
						<label id="rcsu-upload-file" for="rcsu-upload-file"><?php _e("Upload file","rc-suite");?></label>
					</th>
					<td class="forminp forminp-select">
						<input type="file" id="rcsu-upload-file" name="rcsu-upload-file" value="rcsu-upload-file" accept=".csv"/>
						<button id="rcsu-upload-file-button" name="rcsu-upload-file-button" class="button-primary" type="submit" value="rcsu-upload-file-button"><?php _e("Upload CSV","rc-suite");?></button>
					</td>
				</tr>
			</tbody>
			</table>
	  </form>
	  <form method="post" action="<?= get_home_url(get_current_blog_id()) . $_SERVER['PHP_SELF']?>">
	  <tr valign="top">
	  	<table class="form-table">
			<tbody>
				<?php 	// Se añade un campo nonce para probarlo más adelante cuando validemos
						wp_nonce_field( 'replacer_process_file', 'rcsu-suite-nonce' ); 
				?>
				<tr>
					<th scope="row" class="titledesc">
						<label id="rcsu-upload-file" for="rcsu-upload-file"><?php _e("Choose File","rc-suite");?></label>
					</th>
					<td class="forminp forminp-select">
						<p class="description"><?php _e("Choose a file to process","rc-suite"); ?></p>
						<select name="rcsu-choose-file" id="rcsu-choose-file" style="" class="wc-enhanced-select">
						<?php
							$ficheros = array_diff(scandir(PLUGIN_PATH_UPLOAD_FILES), array('..', '.'));

							foreach ($ficheros as $fichero) 
							{
									$selected = (isset($_GET['rcsu-file']) && ($_GET['rcsu-file'] == $fichero)) ? "selected": "";
									echo '<option value="'. $fichero .'" '. $selected .'>' .  $fichero . '</option>';
							} 
						?>
						</select>
					</td>
				</tr>
				<tr>
					<th></th><td><div id="file-msg" name="file-msg"></div></td>
				</tr>
				<tr>
					<th></th><td><div id="file-sub-msg" name="file-sub-msg"></div></td>
				</tr>
				<tr>
					<th></th>
					<td>
						<p class="submit">
							<button id="rcsu-test-file"    name="rcsu-test-file" 	class="button-primary" type="button" value="rcsu-test-file"><?php _e("Test file","rc-suite");?></button>
							<button id="rcsu-process-file" name="rcsu-process-file" class="button-primary" type="button" value="rcsu-process-file"><?php _e("Process file","rc-suite");?></button>
							<button id="rcsu-delete-file"  name="rcsu-delete-file" 	class="button-primary" type="submit" value="rcsu-delete-file"><?php _e("Delete file","rc-suite");?></button>
						</p>
					</td>
				</tr>
			</tbody>
			</table>
			
	  </form>

<?php
}

/*
* 	Pestaña TAGS
*/

add_action( 'rc_suite_settings_tab', 'tags', 1 );

function tags(){
	global $sd_active_tab;
	?>
		<a class="nav-tab <?php echo $sd_active_tab == 'tags' || '' ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'admin.php?page=rc-suite&tab=tags' ); ?>"><?php _e("Tags","rc-suite");?></a>
	<?php
}

/*
* 	Contenido
*/

add_action( 'rc_suite_settings_content', 'tags_content' );

function tags_content() {
	global $sd_active_tab;
	
	if ( '' || 'tags' != $sd_active_tab )
		return;

	// Obtenemos los datos del ultimo tags
	$tags = get_tags(array(
				'taxonomy' => 'post_tag',
				'orderby' => 'name',
				'hide_empty' => false // for development
	 		 ));
?>
    <div class="wrap">
	  <h2><?php _e("Tags", "rc-suite"); ?></h2>
	  <h3><?php _e("Listado", "rc-suite"); ?></h3>
	  <div>
		  <?php
				foreach ($tags as $tag)
				{	?>

				<?php echo $tag->name ?>;<?php echo $tag->count ?><br>	


		<?php	}

		  ?>
	  </div>	 

<?php
}
