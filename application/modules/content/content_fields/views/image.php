<div style="width: 150px; text-align: center; float: left;">
    <a class="choose_image" href="javascript:void(0);" style="display: block; margin-bottom: 5px;">
        <img class="image_thumb" src="<?php echo image_thumb(set_value('field_id_' . $Field->id, $content['src']), 150, 150, FALSE, array('no_image_image' => ADMIN_NO_IMAGE)); ?>" />
    </a>

    <a class="remove_image" href="javascript:void(0);">Remove Image</a><br />
    <a class="choose_image" href="javascript:void(0);">Add Image</a>
    <input class="hidden_file" type="hidden" value="<?php echo set_value('field_id_' . $Field->id, $content['src']); ?>" name="<?php echo 'field_id_' . $Field->id; ?>[src]" />

</div>

<?php if ($Field->settings['output'] == 'image'): ?>
<div style="float: left; margin-left: 15px; width: 220px;">
    <label for="alt"><strong>Alternative Text:</strong></label>
    <input type="text" name="<?php echo 'field_id_' . $Field->id; ?>[alt]" value="<?php echo set_value('field_id_' . $Field->id, $content['alt']); ?>" id="alt" />
</div>
<?php endif; ?>

<div class="clear"></div>
