<div style="width: 150px; text-align: center;">
    <a class="choose_image" href="javascript:void(0);" style="display: block; margin-bottom: 5px;">
        <img class="image_thumb" src="<?php echo image_thumb(set_value('field_id_' . $Field->id, $content), 150, 150, FALSE, array('no_image_image' => ADMIN_NO_IMAGE)); ?>" />
    </a>

    <a class="remove_image" href="javascript:void(0);">Remove Image</a><br />
    <a class="choose_image" href="javascript:void(0);">Add Image</a>
    <input class="hidden_file" type="hidden" value="<?php echo set_value('field_id_' . $Field->id, $content); ?>" name="<?php echo 'field_id_' . $Field->id; ?>" />
</div>
