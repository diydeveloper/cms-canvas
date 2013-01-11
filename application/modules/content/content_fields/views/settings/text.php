<div>
    <label for="type">Allow Inline Editing:</label>
    <span>
        <label><?php echo form_radio(array('name'  => 'settings[inline_editing]', 'value' => '1', 'checked' => set_radio('settings[inline_editing]', '1', ( ! isset($Field->settings['inline_editing']) || $Field->settings['inline_editing']) ? TRUE : FALSE))); ?>Yes</label>
        <label><?php echo form_radio(array('name'  => 'settings[inline_editing]', 'value' => '0', 'checked' => set_radio('settings[inline_editing]', '0', (isset($Field->settings['inline_editing']) && ! $Field->settings['inline_editing']) ? TRUE : FALSE))); ?>No</label>
    </span>
</div>