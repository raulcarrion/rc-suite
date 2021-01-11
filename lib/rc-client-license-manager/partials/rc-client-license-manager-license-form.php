<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 

if ( (isset($_GET['tab']) && 'license' != $_GET['tab']) || !$this->active_tab )
    return;

?>

<div class="wrap">
    <?php $this->get_license_status_banner(); ?>	
    <h2 class="title"> <?php _e("License Key", $this->PLUGIN_SLUG) ?></h2>
    <div id="plugin-msg"></div>
    <p><?php _e("Here you can manage your license key.", $this->PLUGIN_SLUG); ?>
    
    <form method="post" action="<?= get_home_url(get_current_blog_id()) . $_SERVER['PHP_SELF']?>">
        <table class="form-table">
            <tbody>
            <?php 	// Se añade un campo nonce para probarlo más adelante cuando validemos
                    wp_nonce_field( 'license_key', 'rcclm_license' );
            ?>
            <tr valign="top">
                <th scope="row" class="titledesc">
                    <label id="label_<?php echo $this->parent_plugin_slug ?>_rcclm_license_key" for="<?php echo $this->parent_plugin_slug ?>_rcclm_license_key"><?php _e("License Key",$this->PLUGIN_SLUG);?></label>
                </th>
                <td class="forminp forminp-select">
                    <input type="text" id="<?php echo $this->parent_plugin_slug ?>_rcclm_license_key" name="<?php echo $this->parent_plugin_slug ?>_rcclm_license_key" value=" <?php echo $this->get_license_key(); ?> "></input>
                </td>
            </tr>
            <tr valign="top" >
                <th  scope="row" class="titledesc">
                    <label id="label_language" for="language"><?php _e("Status",$this->PLUGIN_SLUG);?></label>
                </th>
                <td class="forminp forminp-select">
                <?php echo $this->get_license_key_status_text(); ?>
                </td>
            </tr>
            </tbody>
        </table>
        <p class="submit">
                <button type="submit" 
                    name="<?php echo $this->parent_plugin_slug ?>_client_license_manager_bt_activate"   
                    class="button-primary"  
                    id="<?php echo $this->parent_plugin_slug ?>_client_license_manager_bt_activate"
                    <?php echo ($this->check_license_key()[1] > 0) ? "disabled" : "" ?> >
                        <?php _e("Activate License Key",$this->PLUGIN_SLUG);?>
                </button>
                <button type="submit" 
                    name="<?php echo $this->parent_plugin_slug ?>_client_license_manager_bt_deactivate" 
                    class="button-primary"  
                    id="<?php echo $this->parent_plugin_slug ?>_client_license_manager_bt_deactivate"
                    <?php echo ($this->check_license_key()[1] < 1) ? "disabled" : "" ?> >
                        <?php _e("Deactivate License Key",$this->PLUGIN_SLUG);?>
                </button>
        </p>
        
    </form>
</div>