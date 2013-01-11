<div>
    <label for="type">Height: <span class="help">Default: 300px</span></label>
    <?php echo form_input(array(
	    'name'  => 'settings[height]', 
	    'style' => 'width: 50px;', 
	    'value' => set_value('settings[height]', ( ! empty($Field->settings['height'])) ? $Field->settings['height'] : '')
    )); ?> px
</div>

<div>
    <label for="type">Allow Inline Editing:</label>
    <span>
        <label><?php echo form_radio(array('name'  => 'settings[inline_editing]', 'value' => '1', 'checked' => set_radio('settings[inline_editing]', '1', ( ! isset($Field->settings['inline_editing']) || $Field->settings['inline_editing']) ? TRUE : FALSE))); ?>Yes</label>
        <label><?php echo form_radio(array('name'  => 'settings[inline_editing]', 'value' => '0', 'checked' => set_radio('settings[inline_editing]', '0', (isset($Field->settings['inline_editing']) && ! $Field->settings['inline_editing']) ? TRUE : FALSE))); ?>No</label>
    </span>
</div>