<div>
    <label for="type">Options: <span class="help">Put each item on a seperate line. <br /><br />Syntax:<br />label or value=label</span></label>
    <?php echo form_textarea(array('name' => 'options', 'value' => set_value('options', ( ! empty($Field->options)) ? $Field->options : ''))); ?>
</div>
