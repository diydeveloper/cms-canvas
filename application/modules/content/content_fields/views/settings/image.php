<div>
    <label for="type">Output Type:</label>
    <?php echo form_dropdown('settings[output]', array('image'  => 'Image', 'image_path' => 'Image Path',), set_value('settings[output]', ( ! empty($Field->settings['output'])) ? $Field->settings['output'] : ''), 'id="output_type"'); ?>
</div>

<div class="image_setting">
    <label for="type">Tag ID:</label>
    <?php echo form_input(array(
        'name'  => 'settings[id]', 
        'value' => set_value('settings[id]', ( ! empty($Field->settings['id'])) ? $Field->settings['id'] : '')
    )); ?>
</div>

<div class="image_setting">
    <label for="type">Class:</label>
    <?php echo form_input(array(
        'name'  => 'settings[class]', 
        'value' => set_value('settings[class]', ( ! empty($Field->settings['class'])) ? $Field->settings['class'] : '')
    )); ?>
</div>

<div class="image_setting">
    <label for="type">Max Width:</label>
    <?php echo form_input(array(
        'name'  => 'settings[max_width]', 
        'style' => 'width: 50px',
        'value' => set_value('settings[max_width]', ( ! empty($Field->settings['max_width'])) ? $Field->settings['max_width'] : '')
    )); ?> px
</div>

<div class="image_setting">
    <label for="type">Max Height:</label>
    <?php echo form_input(array(
        'name'  => 'settings[max_height]', 
        'style' => 'width: 50px',
        'value' => set_value('settings[max_height]', ( ! empty($Field->settings['max_height'])) ? $Field->settings['max_height'] : '')
    )); ?> px
</div>

<div class="image_setting">
    <label for="type">Crop to Dimensions:</label>
    <span>
        <label><?php echo form_radio(array('name'  => 'settings[crop]', 'value' => '1', 'checked' => set_radio('settings[crop]', '1', ( ! empty($Field->settings['crop'])) ? TRUE : FALSE))); ?>Yes</label>
        <label><?php echo form_radio(array('name'  => 'settings[crop]', 'value' => '0', 'checked' => set_radio('settings[crop]', '0', (empty($Field->settings['crop'])) ? TRUE : FALSE))); ?>No</label>
    </span>
</div>

<div class="image_setting">
    <label for="type">Allow Inline Editing:</label>
    <span>
        <label><?php echo form_radio(array('name'  => 'settings[inline_editing]', 'value' => '1', 'checked' => set_radio('settings[inline_editing]', '1', ( ! isset($Field->settings['inline_editing']) || $Field->settings['inline_editing']) ? TRUE : FALSE))); ?>Yes</label>
        <label><?php echo form_radio(array('name'  => 'settings[inline_editing]', 'value' => '0', 'checked' => set_radio('settings[inline_editing]', '0', (isset($Field->settings['inline_editing']) && ! $Field->settings['inline_editing']) ? TRUE : FALSE))); ?>No</label>
    </span>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        $('#output_type').change( function() {
            if ($(this).val() == 'image') {
                $('.image_setting').show();
            } else {
                $('.image_setting').hide();
            }
        });

        $('#output_type').trigger('change');
    });
</script>
