<div>
    <div class="filename" href="javascript:void(0);" style="display: block; margin-bottom: 8px; font-weight: bold;">
        <?php echo set_value('field_id_' . $Field->id, ($Entry_data->{'field_id_' . $Field->id} == '') ? 'No File Added' : $Entry_data->{'field_id_' . $Field->id}); ?>
    </div>

    <a class="remove_file" href="javascript:void(0);">Remove File</a> |
    <a class="choose_file" href="javascript:void(0);">Add File</a>
    <input class="hidden_file" type="hidden" value="<?php echo set_value('field_id_' . $Field->id, isset($Entry_data->{'field_id_' . $Field->id}) ? $Entry_data->{'field_id_' . $Field->id} : ''); ?>" name="<?php echo 'field_id_' . $Field->id; ?>" />
</div>
