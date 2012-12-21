<?php 
    echo form_input(array(
        'name'  =>'field_id_' . $Field->id, 
        'class' =>'datetime', 
        'value' =>set_value('field_id_' . $Field->id, ($content != '') ? date('m/d/Y h:i:s a', strtotime($content)) : '')
    )); 
?>
