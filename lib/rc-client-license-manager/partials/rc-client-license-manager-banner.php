<?php if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

switch ($this->check_license_key()[1]) {
    case 2:
        ?>
            <div class="license_notice">
                <p>	<strong><?php _e("Plugin license has expired!", $this->PLUGIN_SLUG)	?></strong> </p>
                <p> <?php _e("Plugin updating is disabled! Extend your license now!",$this->PLUGIN_SLUG); ?> </p>
            </div>
        <?php
        break;
    case 1:
        break;
    case 0:
        ?>
            <div class="license_notice">
                <p>	<strong><?php _e("License key is not active", $this->PLUGIN_SLUG)	?></strong> </p>
                <p> <?php _e("Please activate you license key",$this->PLUGIN_SLUG); ?> </p>
            </div>
        <?php
        break;
    default:
        ?>
            <div class="license_notice">
                <p>	<strong><?php _e("License key status is unknow", $this->PLUGIN_SLUG)	?></strong> </p>
                <p> <?php _e("Please activate you license key",$this->PLUGIN_SLUG); ?> </p>
            </div>
        <?php
        break;
}

?>