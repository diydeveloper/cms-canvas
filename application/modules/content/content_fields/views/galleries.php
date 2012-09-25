<div>
    <?php 
        echo form_dropdown('field_id_' . $Field->id, $Galleries, set_value('field_id_' . $Field->id, $Entry_data->{'field_id_' . $Field->id})); 
    ?>
</div>

