<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/user.png'); ?>"> <?php echo ($edit_mode) ? 'Group Edit' : 'Group Add'; ?></h1>

        <div class="buttons">
            <a class="button" href="#" id="save" onClick="$('#user_edit_form').submit()"><span>Save</span></a>
        </div>
    </div>
    <div class="content">
        <?php echo form_open(null, 'id="user_edit_form"'); ?>

            <div id="edit-group-tab">
                <div class="form">
                    <div>
                        <?php echo form_label('<span class="required">*</span> User Group Name:', 'name'); ?>
                        <?php echo form_input(array('id' => 'name', 'name' => 'name', 'value' => set_value('name', (isset($Group->name)) ? $Group->name : ''))); ?>
                    </div>

                    <?php if ( ($edit_mode && $Group->modifiable_permissions) // Show if modifiable permissions set to true
                               || ($edit_mode && $Group->type == ADMINISTRATOR && $this->secure->get_group_session()->type == SUPER_ADMIN)  // Override disabled modifiable permissions for super admins on administrators
                               ||  ! $edit_mode ): ?>
                        <div>
                            <?php echo form_label('<span class="required">*</span> Group Type:', 'name'); ?>
                            <div class="fields_wrapper">
                                <span>
                                    <label><?php echo form_radio(array('name' => 'type', 'value' => 'user', 'checked' => set_radio('type', 'user', (isset($Group) && $Group->type == 'user' || empty($Group->group_type)) ? true : false))); ?> User</label><br />
                                    <label><?php echo form_radio(array('name' => 'type', 'value' => 'administrator', 'checked' => set_radio('type', 'administrator', (isset($Group) && $Group->type == 'administrator') ? true : false))); ?> Administrator</label><br />

                                    <?php if ($this->Group_session->type == 'super_admin'): ?>
                                        <label><?php echo form_radio(array('name' => 'type', 'value' => 'super_admin', 'checked' => set_radio('type', 'super_admin', (isset($Group) && $Group->type == 'super_admin') ? true : false))); ?> Super Admin <span style="display: inline;" class="help">(Hides group and its users from administrators and provides additional settings)</span></label>
                                    <?php endif; ?>
                                </span>
                            </div>
                        </div>

                        <div id="permissions_wrapper">
                            <?php echo form_label('Access Permissions:', 'name'); ?>
                            <div class="fields_wrapper">
                                <div class="scrollbox">
                                    <?php $i = 0; ?>
                                    <?php foreach($permission_options as $value => $label): ?>
                                        <div class="<?php echo ($i%2 == 0) ? 'even' : 'odd' ?>">
                                            <label><input type="checkbox" value="<?php echo $value; ?>" name="permissions[access][]" <?php echo set_checkbox('permissions[access][]', $value, ((isset($permissions['access']) && in_array($value, $permissions['access'])) ? TRUE : FALSE)); ?> /><?php echo $label; ?></label>
                                        </div>
                                        <?php $i++; ?>
                                    <?php endforeach; ?>
                                </div>
                                <a onclick="$(this).parent().find(':checkbox').attr('checked', true);">Select All</a> 
                                / 
                                <a onclick="$(this).parent().find(':checkbox').attr('checked', false);">Unselect All</a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        <div class="clear"></div>

        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        $("input[type='radio'][name='type']").change(function() {
            if ($("input[type='radio'][name='type']:checked").val() == 'administrator')
            {
                $('#permissions_wrapper').show();
            }
            else
            {
                $('#permissions_wrapper').hide();
            }
        });

        $("input[type='radio'][name='type']:checked").trigger('change');
    });
</script>
