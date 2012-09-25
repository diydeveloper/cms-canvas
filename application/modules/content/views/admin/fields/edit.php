<?php echo $this->load->view('content/admin/field_types_subnav'); ?>

<div class="box">
    <div class="heading">
        <?php if ($edit_mode): ?>
            <h1><img alt="" src="<?php echo theme_url('assets/images/layout.png'); ?>">Field Edit - <?php echo $Field->label; ?></h1>
        <?php else: ?>
            <h1><img alt="" src="<?php echo theme_url('assets/images/layout.png'); ?>">Field Add</h1>
        <?php endif; ?>

        <div class="buttons">
            <a class="button" href="#" id="save" onClick="$('#form').submit();"><span>Save</span></a>
            <a class="button" href="<?php echo site_url(ADMIN_PATH . '/content/fields/index/' . $Type->id); ?>"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">

        <?php echo form_open('', 'id="form"'); ?>
        <div>
            <div class="form">
                <div>
                    <label for="content_field_type_id"><span class="required">*</span> Field Type:</label>
                    <?php echo form_dropdown('content_field_type_id', $Content_field_types, set_value('content_field_type_id', !empty($Field->content_field_type_id) ? $Field->content_field_type_id : '1'), 'id="content_field_type_id"'); ?>
                </div>
                <div>
                    <label for="label"><span class="required">*</span> Field Label:</label>
                    <?php echo form_input(array('name'=>'label', 'id'=>'label', 'value'=>set_value('label', !empty($Field->label) ? $Field->label : ''))); ?>
                </div>
                <div>
                    <label for="short_tag"><span class="required">*</span> Short Tag:</label>
                    <?php echo form_input(array('name'=>'short_tag', 'id'=>'short_tag', 'value'=>set_value('short_tag', !empty($Field->short_tag) ? $Field->short_tag : ''))); ?>
                </div>
                <div>
                    <label for="required"><span class="required">*</span> Require Field:</label>
                    <span>
                        <label><?php echo form_radio(array('name'=>'required', 'value'=>'1', 'checked'=>set_radio('required', '1', ( ! empty($Field->required)) ? TRUE : FALSE))); ?> Yes</label>
                        <label><?php echo form_radio(array('name'=>'required', 'value'=>'0', 'checked'=>set_radio('required', '0', (empty($Field->required)) ? TRUE : FALSE))); ?> No</label>
                    </span>
                </div>

                <span id="config"><?php echo $setting_fields; ?></span>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    $('#content_field_type_id').change( function() {
        $.post("<?php echo site_url(ADMIN_PATH . '/content/fields/settings'); ?>", { content_field_type_id: $('#content_field_type_id').val() <?php echo ($edit_mode) ? ', field_id: ' . $Field->id : '' ?> }, function(data) {
            $('#config').html(data);
        });
    });

    $('#content_field_type_id').trigger('change');

    <?php if ( ! $edit_mode): ?>
        $('#label').keyup( function(e) {
            $('#short_tag').val($(this).val().toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9\-_]/g, ''))
        });
    <?php endif; ?>
</script>
