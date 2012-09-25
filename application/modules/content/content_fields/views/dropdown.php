<div>
    <?php 
        echo form_dropdown('field_id_' . $Field->id, $Field->options, set_value('field_id_' . $Field->id, $Entry_data->{'field_id_' . $Field->id})); 
    ?>
</div>
