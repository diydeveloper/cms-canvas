<div>
    <label for="type">Rows: <span class="help">Default: 5</span></label>
    <?php echo form_input(array('name' => 'settings[rows]', 'value' => set_value('settings[rows]', ( ! empty($Field->settings['rows'])) ? $Field->settings['rows'] : ''))); ?>
</div>
