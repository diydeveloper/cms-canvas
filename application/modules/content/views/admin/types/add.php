<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/layout.png'); ?>">Add Content Type</h1>

        <div class="buttons">
            <a class="button" href="#" onClick="$('#layout_add').submit();"><span>Next &raquo;</span></a>
        </div>
    </div>
    <div class="content">

        <div class="form">
            <?php echo form_open(null, 'id="layout_add"'); ?>
            <div>
                <?php echo form_label('<span class="required">*</span> Title:', 'title'); ?>
                <?php echo form_input(array('name'=>'title', 'id'=>'title', 'value'=>set_value('title'))); ?>
            </div>
            <div>
                <?php echo form_label('<span class="required">*</span> Short Name:<span class="help">Identifier containing no spaces</span>', 'short_name'); ?>
                <?php echo form_input(array('name'=>'short_name', 'id'=>'short_name', 'value'=>set_value('short_name'))); ?>
            </div>
            <div id="theme_layout_div">
                <?php echo form_label('<span class="required">*</span> Theme Layout:', 'theme_layout'); ?>
                <?php echo form_dropdown('theme_layout', $theme_layouts, set_value('theme_layout', ' ')); ?>
            </div>
            <div>
                <?php echo form_label('Enable Dynamic Routing:', 'enable_dynamic_routing'); ?>
                <?php echo form_checkbox(array('name'=>'enable_dynamic_routing', 'id'=>'enable_dynamic_routing', 'value'=>'1', 'checked' => set_checkbox('enable_dynamic_routing', '1'))); ?>
            </div>
            <div id="dynamic_route_div" style="display: none;">
                <?php echo form_label('<span class="required">*</span> Dynamic Route:<span class="help">Path and identifieer of the content type. When rendered, any additional url segments appended are considered parameters.</span>', 'dynamic_route'); ?>
                <?php echo site_url(); ?><?php echo form_input(array('name'=>'dynamic_route', 'id'=>'dynamic_route', 'value'=>set_value('dynamic_route'))); ?>
            </div>
            <div>
                <?php echo form_label('<span class="required">*</span> Enable Versioning:', 'enable_versioning'); ?>
                <span>
                    <label><?php echo form_radio(array('name'=>'enable_versioning', 'value'=>'1', 'checked' => set_radio('enable_versioning', '1'))); ?> Yes</label>
                    <label><?php echo form_radio(array('name'=>'enable_versioning', 'value'=>'0', 'checked' => set_radio('enable_versioning', '0', TRUE))); ?> No</label>
                </span>
            </div>
            <div style="display: none;" id="max_revisions_div">
                <?php echo form_label('<span class="required">*</span> Max Revisions:<span class="help">The maximum number of revisions to keep for each entry.</span>', 'max_revisions'); ?>
                <?php echo form_input(array('name'=>'max_revisions', 'id'=>'max_revisions', 'value'=>set_value('max_revisions', '5'), 'style'=>'width: 50px;')); ?>
                <span class="ex">25 Max</span>
            </div>
            <div>
                <?php echo form_label('Number of Entries Allowed:<span class="help">Number of entries allowed to be created with this content type</span>', 'entries_allowed'); ?>
                <?php echo form_input(array('name'=>'entries_allowed', 'id'=>'entries_allowed', 'value'=>set_value('entries_allowed'), 'class'=>'short')); ?>
                <span class="ex">Leave blank for unlimited</span>
            </div>
            <div>
                <?php echo form_label('Category Group:<span class="help">Assign a set of categories to this content type so that authors can specify categories for entries.</span>', 'category_group_id'); ?>
                <?php echo form_dropdown('category_group_id', $category_groups, set_value('category_gorup_id')); ?>
            </div>
            <div>
                <?php echo form_label('<span class="required">*</span> Restrict Admin Access:<span class="help">Only make this content type\'s entries visible to specified groups.</span>', 'restrict_admin_access'); ?>
                <div class="multi_fields">
                    <span>
                        <label><?php echo form_radio(array('name'=>'restrict_admin_access', 'value'=>'1', 'checked' => set_radio('restrict_admin_access', '1'))); ?> Yes</label>
                        <label><?php echo form_radio(array('name'=>'restrict_admin_access', 'value'=>'0', 'checked' => set_radio('restrict_admin_access', '0', TRUE))); ?> No</label>
                    </span>
                    <div id="admin_access_field">
                        <div class="fields_wrapper">
                            <div class="scrollbox">
                                <?php $i = 0; ?>
                                <?php foreach($Admin_groups as $Group): ?>
                                    <div class="<?php echo ($i%2 == 0) ? 'even' : 'odd' ?>">
                                        <label>
                                            <input type="checkbox" value="<?php echo $Group->id; ?>" name="selected_admin_groups[]" <?php echo set_checkbox('selected_admin_groups[]', $Group->id); ?>>
                                            <?php echo $Group->name; ?>
                                        </label>
                                    </div>
                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            </div>
                            <a onclick="$(this).parent().find(':checkbox').attr('checked', true);">Select All</a> 
                            / 
                            <a onclick="$(this).parent().find(':checkbox').attr('checked', false);">Unselect All</a>
                        </div>
                    </div>
                </div>
            </div>
            <div>
                <?php echo form_label('<span class="required">*</span> Access:<span class="help">Make this content type and its entries accessible to:<br /><br />NOTE: Permission checks are only applied when accessing pages and dynamic routes directly via URL.</span>', 'access'); ?>
                <div class="multi_fields">
                    <span>
                        <label><?php echo form_radio(array('name'=>'access', 'value'=>'0', 'checked' => set_radio('access', '0', TRUE))); ?> Everyone</label><br />
                        <label><?php echo form_radio(array('name'=>'access', 'value'=>'1', 'checked' => set_radio('access', '1'))); ?> Any Logged In User</label><br />
                        <label><?php echo form_radio(array('name'=>'access', 'value'=>'2', 'checked' => set_radio('access', '2'))); ?> Specified Groups</label>
                    </span>
                    <div id="access_field">
                        <div class="fields_wrapper">
                            <div class="scrollbox">
                                <?php $i = 0; ?>
                                <?php foreach($Groups as $Group): ?>
                                    <div class="<?php echo ($i%2 == 0) ? 'even' : 'odd' ?>">
                                        <label>
                                            <input type="checkbox" value="<?php echo $Group->id; ?>" name="restrict_to[]" <?php echo set_checkbox('restrict_to[]', $Group->id); ?>>
                                            <?php echo $Group->name; ?>
                                        </label>
                                    </div>
                                    <?php $i++; ?>
                                <?php endforeach; ?>
                            </div>
                            <a onclick="$(this).parent().find(':checkbox').attr('checked', true);">Select All</a> 
                            / 
                            <a onclick="$(this).parent().find(':checkbox').attr('checked', false);">Unselect All</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php echo form_close(); ?>
        </div>
    </div>
</div>

<?php js_start(); ?>
<script type="text/javascript">
    $(document).ready( function() {
        // Auto fill short name based on title
        $('#title').keyup( function(e) {
            $('#short_name').val($(this).val().toLowerCase().replace(/\s+/g, '_').replace(/[^a-z0-9\-_]/g, ''))
        });

        // Hide / Show Dynamic Routing
        $('#enable_dynamic_routing').change( function(e) {
            if ($('#enable_dynamic_routing').is(':checked'))   
            {
                $('#dynamic_route_div').show();
            }
            else
            {
                $('#dynamic_route_div').hide();
            }
        });
        $('#enable_dynamic_routing').trigger('change');

        // Hide / Show Admin Acess
        $('input[name="restrict_admin_access"]').change( function(e) {
            if ($('input[name="restrict_admin_access"]:checked').val() == 1)   
            {
                $('#admin_access_field').show();
            }
            else
            {
                $('#admin_access_field').hide();
            }
        });
        $('input[name="restrict_admin_access"]').trigger('change');

        // Hide / Show Acess
        $('input[name="access"]').change( function(e) {
            if ($('input[name="access"]:checked').val() == 2)   
            {
                $('#access_field').show();
            }
            else
            {
                $('#access_field').hide();
            }
        });
        $('input[name="access"]').trigger('change');

        // Hide / Show Max Revisions
        $('input[name="enable_versioning"]').change( function(e) {
            if ($('input[name="enable_versioning"]:checked').val() == 1)   
            {
                $('#max_revisions_div').show();
            }
            else
            {
                $('#max_revisions_div').hide();
            }
        });
        $('input[name="enable_versioning"]').trigger('change');
    });
</script>
<?php js_end(); ?>