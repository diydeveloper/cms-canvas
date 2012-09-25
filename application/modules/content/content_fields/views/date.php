<?php 
    echo form_input(array(
        'name'  =>'field_id_' . $Field->id, 
        'class' =>'datepicker', 
        'value' =>set_value('field_id_' . $Field->id, ($Entry_data->{'field_id_' . $Field->id} != '') ? date('m/d/Y', strtotime($Entry_data->{'field_id_' . $Field->id})) : '')
    )); 
?>
