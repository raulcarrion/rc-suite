<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
} 

?>
<a class="nav-tab <?php echo $_GET['tab'] == 'license' || $this->active_tab ? 'nav-tab-active' : ''; ?>" href="<?php echo admin_url( 'admin.php?page='.$this->get_parent_plugin_slug().'&tab=license' ); ?>">
	<?php _e("License Key",$this->get_plugin_slug());?>
</a>