<div>
    <span>
    <?php foreach($Field->options as $value => $label): ?>
        <div>
            <label><input type="checkbox" name="<?php echo 'field_id_' . $Field->id; ?>[]" value="<?php echo $value; ?>" <?php echo set_checkbox('field_id_' . $Field->id . '[]', $value, (in_array($value, $content)) ? TRUE : FALSE); ?> /> <?php echo $label; ?></label>
        </div>
    <?php endforeach; ?>
    </span>
</div>

