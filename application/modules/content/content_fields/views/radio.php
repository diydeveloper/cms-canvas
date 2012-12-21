<div>
    <span>
    <?php foreach($Field->options as $value => $label): ?>
        <div>
            <label><input type="radio" name="<?php echo 'field_id_' . $Field->id; ?>" value="<?php echo $value; ?>" <?php echo set_radio('field_id_' . $Field->id, $value, ($content == $value) ? TRUE : FALSE); ?> /> <?php echo $label; ?></label>
        </div>
    <?php endforeach; ?>
    </span>
</div>
