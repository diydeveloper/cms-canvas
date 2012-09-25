<div>
    <?php 
        echo form_textarea(array(
            'name' => 'field_id_' . $Field->id, 
            'value' => set_value('field_id_' . $Field->id, $Entry_data->{'field_id_' . $Field->id}),
            'rows' => ( ! empty($Field->settings['rows'])) ? $Field->settings['rows'] : 5,
        )); 
    ?>
</div>
