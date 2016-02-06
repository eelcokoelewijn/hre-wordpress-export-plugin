<?php
/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://hetrondjeeilanden.nl
 * @since      1.0.0
 *
 * @package    HRE Exporter
 * @subpackage Plugin_Name/admin/partials
 */
$product_ids = $this->hre_get_product_ids();
?>


<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">
    <h2><?php _e('Registration Export',$this->plugin_name)?></h2>
    <form method="post" name="hre_exporter_form">
    <input type="hidden" name="download" value="true"/>
        <h3><?php _e('Select product and click export',$this->plugin_name)?></h3>
        <select name="registration_type">
            <?php
            foreach ($product_ids as $key => $value) {
              ?>
              <option value=<?=$key?>><?=$value?></option>
              <?php
            }
            ?>
        </select>
        <p class="submit"><input class="btn-submit" type="submit" name="Submit" value="<?php _e('Export',$this->plugin_name)?>" /></p>
    </form>
</div>
