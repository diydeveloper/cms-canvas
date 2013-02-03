<div>
    <span>
    <?php foreach($Field->options as $value => $label): ?>
        <div>
            <label><input type="checkbox" name="<?php echo 'field_id_' . $Field->id; ?>[]" value="<?php echo $value; ?>" <?php echo set_checkbox('field_id_' . $Field->id . '[]', $value, (in_array($value, $content)) ? TRUE : FALSE); ?> /> <?php echo $label; ?></label>
            <?php 
            /* 
             * Since browsers do not POST checkboxes if they are unchecked...
             * This hidden field is used to identify that the checkboxes were present when the form was submitted. 
            */
            ?>
            <input type="hidden" name="<?php echo 'field_id_' . $Field->id; ?>_checkbox" value="1" />
        </div>
    <?php endforeach; ?>
    </span>
</div>

