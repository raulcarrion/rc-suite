<?php

/**
 *  Dibuja los detalles de un fichero de reemplazos 
 * 
 * @since    1.0.0
 * @var      $details   array con la estructura del fichero
 */

function rcsu_html_file_replacer_details($details)
{
    ?>
    <h4><?php _e("File structure","rc-comments"); ?></h4>
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <td class="manage-column column-cb check-column"></td>
                <th class="manage-column column-order_number column-primary"><?php _e("Table","rc-suite") ?></th>
                <th class="manage-column column-order_number column-primary"><?php _e("Field","rc-suite") ?></th>
                <th class="manage-column column-order_number column-primary"><?php _e("String to replace","rc-suite") ?></th>
                <th class="manage-column column-order_number column-primary"><?php _e("Replacement string","rc-suite") ?></th>
            </tr>
        </thead>
        <tbody id="the-list">
    <?php
    foreach ($details as $detail)
    {
        foreach ($detail as $key => $value)
        {
            ?>
            
            <tr>
                <td class="manage-column column-cb check-column"></td>
                <td class="manage-column column-cb check-column"><?php echo $key?></td>
                <td class="manage-column column-cb check-column"><?php echo $value[0]?></td>
                <td class="manage-column column-cb check-column"><?php echo $value[1]?></td>
                <td class="manage-column column-cb check-column"><?php echo $value[2]?></td>
            </tr>
            <?php
        }
    }
    ?>
        </tbody>
    </table>
    <?php
}

/**
 *  Dibuja los detalles de un test de fichero de reemplazos 
 * 
 * @since    1.0.0
 * @var      $details   array con la estructura del fichero
 */

function rcsu_html_file_replacer_test($details)
{
    ?>
    <h4><?php _e("File test result","rc-comments"); ?></h4>
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <td class="manage-column column-cb check-column"></td>
                <th class="manage-column column-order_number column-primary"><?php _e("Table","rc-suite") ?></th>
                <th class="manage-column column-order_number column-primary"><?php _e("Field","rc-suite") ?></th>
                <th class="manage-column column-order_number column-primary"><?php _e("String to replace","rc-suite") ?></th>
                <th class="manage-column column-order_number column-primary"><?php _e("Matches","rc-suite") ?></th>
            </tr>
        </thead>
        <tbody id="the-list">
    <?php
    foreach ($details as $detail)
    {
            ?>
            
            <tr>
                <td class="manage-column column-cb check-column"></td>
                <td class="manage-column column-cb check-column"><?php echo $detail['Tabla']?></td>
                <td class="manage-column column-cb check-column"><?php echo $detail['Campo']?></td>
                <td class="manage-column column-cb check-column"><?php echo $detail['Cadena']?></td>
                <td class="manage-column column-cb check-column"><?php echo $detail['Coincidencias']?></td>
            </tr>
            <?php
    }
    ?>
        </tbody>
    </table>
    <?php
}

/**
 *  Dibuja los detalles de un test de fichero de reemplazos 
 * 
 * @since    1.0.0
 * @var      $details   array con la estructura del fichero
 */

function rcsu_html_file_replacer_replaces($details)
{
    ?>
    <h4><?php _e("Replacements done","rc-comments"); ?></h4>
    <table class="wp-list-table widefat fixed striped posts">
        <thead>
            <tr>
                <td class="manage-column column-cb check-column"></td>
                <th class="manage-column column-order_number column-primary"><?php _e("Table","rc-suite") ?></th>
                <th class="manage-column column-order_number column-primary"><?php _e("Field","rc-suite") ?></th>
                <th class="manage-column column-order_number column-primary"><?php _e("String to replace","rc-suite") ?></th>
                <th class="manage-column column-order_number column-primary"><?php _e("Replaces","rc-suite") ?></th>
            </tr>
        </thead>
        <tbody id="the-list">
    <?php
    foreach ($details as $detail)
    {
            ?>
            
            <tr>
                <td class="manage-column column-cb check-column"></td>
                <td class="manage-column column-cb check-column"><?php echo $detail['Tabla']?></td>
                <td class="manage-column column-cb check-column"><?php echo $detail['Campo']?></td>
                <td class="manage-column column-cb check-column"><?php echo $detail['Cadena']?></td>
                <td class="manage-column column-cb check-column"><?php echo $detail['Afectadas']?></td>
            </tr>
            <?php
    }
    ?>
        </tbody>
    </table>
    <?php
}