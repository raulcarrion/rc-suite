<?php

/**
 * @link       https://www.raulcarrion.com/
 * @since      1.0.0
 *
 * @package    Rc_Suite
 * @subpackage Rc_Suite/admin/partials
 */

function rc_suite_admin_page(){
?>
	<h1>RC Suite</h1>
    <p>Configura el plugin de moda.</p>
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
	     	</tbody>
	     </table>
	     <p class="submit">
	     	<button name="save" class="button-primary" type="submit" value="Guardar los cambios">Guardar los cambios</button>
	     </p>
	     <input type="hidden" name="action" value="update" />
	     <input type="hidden" name="page_options" value="rc_anti_cache_css_enabled,rc_anti_publi_plugins_enabled,rc_login_customer_enabled, rc_parent_css_enabled,rc_divi_projects_disabled_enabled" />
	  </form>
	</div>
<?php
}