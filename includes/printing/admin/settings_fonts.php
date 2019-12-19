<?php
    if(isset($_POST['wc_logo_fonts_field'])){
        if (wp_verify_nonce($_POST['wc_logo_fonts_field'], 'wc_logo_fonts')) {
            update_option('wcbl_fonts', $_POST['fonts']);
        }
    }
    
    $wcbl_fonts = get_option('wcbl_fonts');
?>
<style>
    ul.fonts-list{
        -webkit-column-count: 6; /* Chrome, Safari, Opera */
        -moz-column-count: 6; /* Firefox */
        column-count: 6;
    }
</style>
<h1><?php _e('Embroidery Fonts');?></h1>
<form name="logo_pricing" method="POST" action="">
    <?php wp_nonce_field('wc_logo_fonts', 'wc_logo_fonts_field'); ?>
    <table class="form-table">
        <tbody>
            <tr valign="top">
                <td>
                <a class="seelct_all_fonts" href="javascript:void(0);"><?php _e('Select All', 'wc_printing_logo');?></a>
                <a class="deseelct_all_fonts" href="javascript:void(0);"><?php _e('Deselect All', 'wc_printing_logo');?></a>
                <ul class="fonts-list">
                    <?php
                    if(!is_array($wcbl_fonts)){
                        $wcbl_fonts = array();
                    }
                    foreach($fonts as $font_key=>$font_label){
                        $checked = '';
                        if(in_array($font_key, $wcbl_fonts)){
                            $checked = ' checked';
                        }
                        ?>
                        <li>
                            <input type="checkbox" name="fonts[]" id="font_<?php echo $font_key;?>" value="<?php echo $font_key;?>"<?php echo $checked?>>
                            <label for="font_<?php echo $font_key;?>"><?php echo $font_label;?></label>
                        </li>
                        <?php
                    }
                    ?>
                </div>
                
                </td>
            </tr>            
        </tbody>
    </table>
    <?php submit_button('Save', 'primary');?>
</form>