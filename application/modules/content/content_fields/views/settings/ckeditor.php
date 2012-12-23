<div>
    <label for="type">Height: <span class="help">Default: 300px</span></label>
    <?php echo form_input(array(
	    'name'  => 'settings[height]', 
	    'style' => 'width: 50px;', 
	    'value' => set_value('settings[height]', ( ! empty($Field->settings['height'])) ? $Field->settings['height'] : '')
    )); ?> px
</div>
