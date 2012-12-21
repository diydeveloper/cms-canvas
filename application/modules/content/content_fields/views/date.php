<?php 
    echo form_input(array(
        'name'  =>'field_id_' . $Field->id, 
        'class' =>'datepicker', 
        'value' =>set_value('field_id_' . $Field->id, ($content != '') ? date('m/d/Y', strtotime($content)) : '')
    )); 
?>
