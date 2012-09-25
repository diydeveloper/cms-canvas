<?php
$email = array(
                'id'   => 'email',
                'name' => 'email',
                'value' => set_value('email', (isset($User->email)) ? $User->email : ''),
            );

$first_name = array(
                'id'   => 'first_name',
                'name' => 'first_name',
                'value' => set_value('first_name', (isset($User->first_name)) ? $User->first_name : ''),
            );

$last_name = array(
                'id'   => 'last_name',
                'name' => 'last_name',
                'value' => set_value('last_name', (isset($User->last_name)) ? $User->last_name : ''),
            );

$phone = array(
                'id'   => 'phone',
                'name' => 'phone',
                'value' => set_value('phone', (isset($User->phone)) ? $User->phone : ''),
            );

$address = array(
                'id'   => 'address',
                'name' => 'address',
                'value' => set_value('address', (isset($User->address)) ? $User->address : ''),
            );

$city = array(
                'id'   => 'city',
                'name' => 'city',
                'value' => set_value('city', (isset($User->city)) ? $User->city : ''),
            );

$states = unserialize(STATES);

$zip = array(
                'id'   => 'zip',
                'name' => 'zip',
                'value' => set_value('zip', (isset($User->zip)) ? $User->zip : ''),
            );

$skype_name = array(
                'id'   => 'skype_name',
                'name' => 'skype_name',
                'value' => set_value('skype_name', (isset($User->skype_name)) ? $User->skype_name : ''),
            );

$ichat_name = array(
                'id'   => 'ichat_name',
                'name' => 'ichat_name',
                'value' => set_value('ichat_name', (isset($User->ichat_name)) ? $User->ichat_name : ''),
            );
?>

<?php echo form_open(); ?>
    <div>
        <div class="form">

            <fieldset>
                <legend>Contact Info</legend>
                <div>
                    <?php echo form_label('<span class="required">*</span> Email:', 'email'); ?>
                    <?php echo form_input($email); ?>
                </div>

                <div>
                    <?php echo form_label('<span class="required">*</span> First Name:', 'first_name'); ?>
                    <?php echo form_input($first_name); ?>
                </div>

                <div>
                    <?php echo form_label('<span class="required">*</span> Last Name:', 'last_name'); ?>
                    <?php echo form_input($last_name); ?>
                </div>

                <div>
                    <?php echo form_label('<span class="required">*</span> Phone:', 'phone'); ?>
                    <?php echo form_input($phone); ?>
                </div>

                <div>
                    <?php echo form_label('<span class="required">*</span> Address:', 'address'); ?>
                    <?php echo form_input($address); ?>
                </div>

                <div>
                    <?php echo form_label('<span class="required">*</span> City:', 'city'); ?>
                    <?php echo form_input($city); ?>
                </div>

                <div>
                    <?php echo form_label('<span class="required">*</span> State:', 'state'); ?>
                    <?php echo form_dropdown('state', $states, set_value('state', (isset($User->state)) ? $User->state : ''), 'id="state"'); ?>
                </div>

                <div>
                    <?php echo form_label('<span class="required">*</span> Zip:', 'zip'); ?>
                    <?php echo form_input($zip); ?>
                </div>

                <div>
                    <?php echo form_label('Skype Name:', 'skype_name'); ?>
                    <?php echo form_input($skype_name); ?>
                </div>

                <div>
                    <?php echo form_label('iChat Name:', 'ichat_name'); ?>
                    <?php echo form_input($ichat_name); ?>
                </div>
            </fieldset>

            <fieldset>
                <legend>Student Info</legend>
                <?php echo $Form->fields('field_2', 'field_7'); ?>
            </fieldset>

            <fieldset>
                <legend>Personal Info</legend>
                <?php echo $Form->fields('field_8', 'field_8'); ?>
                <div>
                    <label>Height:</label>
                    <span>
                        <?php echo $Form->field('field_9'); ?> foot &nbsp;
                        <?php echo $Form->field('field_10'); ?> inches
                    </span>
                </div>
                <div>
                    <?php echo $Form->label('field_1'); ?>
                    <span>
                        <?php echo $Form->field('field_1'); ?> pounds
                    </span>
                </div>
                <?php echo $Form->fields('field_11', 'field_12'); ?>
            </fieldset>

            <fieldset>
                <legend>Academic Info</legend>
                <?php echo $Form->fields('field_13', 'field_16'); ?>
            </fieldset>

            <fieldset>
                <legend>Parent Info</legend>
                <?php echo $Form->fields('field_17', 'field_19'); ?>
            </fieldset>

            <br />
            <div class="aligncenter">
                <input class="submit" type="submit" value="Save" />
            </div>

        </div>
    </div>
    <div class="clear"></div>

    <?php echo form_close(); ?>
