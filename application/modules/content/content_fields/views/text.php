<?php 
    echo form_input(array(
        'name'  =>'field_id_' . $Field->id, 
        'value' =>set_value('field_id_' . $Field->id, $Entry_data->{'field_id_' . $Field->id})
    )); 
?>
