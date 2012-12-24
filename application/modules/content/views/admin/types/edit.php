<?php echo $this->load->view('content/admin/field_types_subnav'); ?>

<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/layout.png'); ?>">Content Type Edit - <?php echo $Content_type->title; ?> (<?php echo $Content_type->short_name; ?>)</h1>

        <div class="buttons">
            <a class="button" href="#" id="save"><span>Save</span></a>
            <a class="button" href="#" id="save_exit"><span>Save &amp; Exit</span></a>
            <a class="button" href="<?php echo site_url(ADMIN_PATH . '/content/types'); ?>"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">

        <?php echo form_open('', 'id="layout_edit"'); ?>
        <div>
            <table class="list">
                <thead>
                    <tr>
                        <th width="220">Fields</th>
                        <th>Short Tag (click on tags below to copy)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Entry Title</td>
                        <td><span style="position:relative;"><a class="copy_text" href="#">{{ title }}</a></span></td>
                    </tr>
                    <?php foreach($Fields as $Field): ?>
                        <tr>
                            <td><?php echo $Field->label; ?></td>
                            <td><span style="position:relative;"><a class="copy_text" href="javascript:void(0);">{{ <?php echo $Field->short_tag; ?> }}</a></span></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div id="tabs">
                <ul class="htabs">
                    <li><a href="#html-tab">HTML</a></li>
                    <li><a href="#page-head-tab">Page &lt;head&gt;</a></li>
                    <li><a href="#revisions-tab">Revisions</a></li>
                    <li><a href="#settings-tab">Settings</a></li>
                </ul>
                <div id="html-tab">
                    <?php echo form_textarea(array('name'=>'layout', 'id'=>'layout', 'value'=>set_value('layout', !empty($Content_type->layout) ? $Content_type->layout : ''))); ?>
                </div>
                <div id="page-head-tab">
                    <p class="info">Include custom JavaScript, CSS, and/or meta information in the <strong>&lt;head&gt;</strong> block of this content type's pages.</p>

                    <?php echo form_textarea(array('name'=>'page_head', 'id'=>'page_head', 'value'=>set_value('page_head', !empty($Content_type->page_head) ? $Content_type->page_head : ''))); ?>
                </div>
                <div id="revisions-tab">
                    <?php $Content_type->content_type_revisions->order_by('id', 'desc')->get(); $r = $Content_type->content_type_revisions->result_count(); ?>
                    <table class="list">
                        <thead>
                            <tr>
                                <th>Revision</th>
                                <th>Author</th>
                                <th>Date</th>
                                <th class="right">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($Content_type->content_type_revisions->exists()): ?>
                                <?php foreach($Content_type->content_type_revisions as $Revision): ?>
                                    <tr>
                                        <td>Revision <?php echo $r; ?></td>
                                        <td><?php echo $Revision->author_name; ?></td>
                                        <td><?php echo date('m/d/Y h:i a', strtotime($Revision->revision_date)); ?></td>
                                        <td class="right">
                                            <?php if ( ($revision_id == '' && $r == $Content_type->content_type_revisions->result_count()) 
                                                || $Revision->id == $revision_id): ?>
                                                <strong>Currently Loaded</strong>
                                            <?php else: ?>
                                                [ <a href="<?php echo site_url(ADMIN_PATH . '/content/types/edit/' . $Revision->content_type_id . '/' . $Revision->id); ?> ">Load Revision</a> ]</td>
                                            <?php endif; ?>
                                    </tr>
                                    <?php $r--; ?>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td class="center" colspan="4">No revisions have been saved.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div id="settings-tab">
                    <div class="form">
                        <div>
                            <label for="title"><span class="required">*</span> Title:</label>
                            <?php echo form_input(array('name'=>'title', 'id'=>'title', 'value'=>set_value('title', !empty($Content_type->title) ? $Content_type->title : ''))); ?>
                        </div>
                        <div>
                            <label for="short_name"><span class="required">*</span> Short Name:</label>
                            <?php echo form_input(array('name'=>'short_name', 'id'=>'short_name', 'value'=>set_value('short_name', !empty($Content_type->short_name) ? $Content_type->short_name : ''))); ?>
                        </div>
                        <div class="field_spacing">
                            <label for="theme_layout"><span class="required">*</span> Theme Layout:</label>
                            <?php echo form_dropdown('theme_layout', $theme_layouts, set_value('theme_layout', !empty($Content_type->theme_layout) ? $Content_type->theme_layout : '')); ?>
                        </div>
                        <div>
                            <?php echo form_label('Enable Dynamic Routing:', 'enable_dynamic_routing'); ?>
                            <?php echo form_checkbox(array('name'=>'enable_dynamic_routing', 'id'=>'enable_dynamic_routing', 'value'=>'1', 'checked' => set_checkbox('enable_dynamic_routing', '1',  !empty($Content_type->dynamic_route) ? TRUE : FALSE))); ?>
                        </div>
                        <div style="display: none;" id="dynamic_route_div">
                            <label for="dynamic_route"><span class="required">*</span> Dynamic Route:<span class="help">Path and identifieer of the content type. When rendered, any additional url segments appended are considered parameters.</span></label>
                            <?php echo site_url(); ?><?php echo form_input(array('name'=>'dynamic_route', 'id'=>'dynamic_route', 'value'=>set_value('dynamic_route', !empty($Content_type->dynamic_route) ? $Content_type->dynamic_route : ''))); ?>
                        </div>
                        <div>
                            <?php echo form_label('<span class="required">*</span> Enable Versioning:', 'enable_versioning'); ?>
                            <span>
                                <label><?php echo form_radio(array('name'=>'enable_versioning', 'value'=>'1', 'checked' => set_radio('enable_versioning', '1', !empty($Content_type->enable_versioning) ? TRUE : FALSE))); ?> Yes</label>
                                <label><?php echo form_radio(array('name'=>'enable_versioning', 'value'=>'0', 'checked' => set_radio('enable_versioning', '0', empty($Content_type->enable_versioning) ? TRUE : FALSE))); ?> No</label>
                            </span>
                        </div>
                        <div style="display: none;" id="max_revisions_div">
                            <?php echo form_label('<span class="required">*</span> Max Revisions:<span class="help">The maximum number of revisions to keep for each entry.</span>', 'max_revisions'); ?>
                            <?php echo form_input(array('name'=>'max_revisions', 'id'=>'max_revisions', 'value'=>set_value('max_revisions', !empty($Content_type->max_revisions) ? $Content_type->max_revisions : '5'), 'style'=>'width: 50px;')); ?>
                            <span class="ex">25 Max</span>
                        </div>
                        <div>
                            <?php echo form_label('Number of Entries Allowed:<span class="help">Number of entries allowed to be created with this content type</span>', 'entries_allowed'); ?>
                            <?php echo form_input(array('name'=>'entries_allowed', 'id'=>'entries_allowed', 'value'=>set_value('entries_allowed', isset($Content_type->entries_allowed) ? $Content_type->entries_allowed : ''), 'class'=>'short')); ?>
                            <span class="ex">Leave blank for unlimited</span>
                        </div>
                        <div>
                            <?php echo form_label('Category Group:<span class="help">Assign a set of categories to this content type so that authors can specify categories for entries.</span>', 'category_group_id'); ?>
                            <?php echo form_dropdown('category_group_id', $category_groups, set_value('category_gorup_id', !empty($Content_type->category_group_id) ? $Content_type->category_group_id : ''), 'id="category_group_id"'); ?>
                        </div>
                        <div>
                            <?php echo form_label('<span class="required">*</span> Restrict Admin Access:<span class="help">Only make this content type\'s entries visible to specified groups.</span>', 'restrict_admin_access'); ?>
                            <div class="multi_fields">
                                <span>
                                    <label><?php echo form_radio(array('name'=>'restrict_admin_access', 'value'=>'1', 'checked' => set_radio('restrict_admin_access', '1', !empty($Content_type->restrict_admin_access) ?  TRUE : FALSE))); ?> Yes</label>
                                    <label><?php echo form_radio(array('name'=>'restrict_admin_access', 'value'=>'0', 'checked' => set_radio('restrict_admin_access', '0', empty($Content_type->restrict_admin_access) ?  TRUE : FALSE))); ?> No</label>
                                </span>
                                <div id="admin_access_field">
                                    <div class="fields_wrapper">
                                        <div class="scrollbox">
                                            <?php $i = 0; ?>
                                            <?php foreach($Admin_groups as $Group): ?>
                                                <div class="<?php echo ($i%2 == 0) ? 'even' : 'odd' ?>">
                                                    <label>
                                                        <input type="checkbox" value="<?php echo $Group->id; ?>" name="selected_admin_groups[]" <?php echo set_checkbox('selected_admin_groups[]', $Group->id, in_array($Group->id, $current_admin_groups) ? TRUE : FALSE); ?>>
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
                                    <label><?php echo form_radio(array('name'=>'access', 'value'=>'0', 'checked' => set_radio('access', '0', empty($Content_type->access) ? TRUE : FALSE))); ?> Everyone</label><br />
                                    <label><?php echo form_radio(array('name'=>'access', 'value'=>'1', 'checked' => set_radio('access', '1', ($Content_type->access == 1) ? TRUE : FALSE))); ?> Any Logged In User</label><br />
                                    <label><?php echo form_radio(array('name'=>'access', 'value'=>'2', 'checked' => set_radio('access', '2', ($Content_type->access == 2) ? TRUE : FALSE))); ?> Specified Groups</label>
                                </span>
                                <div id="access_field">
                                    <div class="fields_wrapper">
                                        <div class="scrollbox">
                                            <?php $i = 0; ?>
                                            <?php foreach($Groups as $Group): ?>
                                                <div class="<?php echo ($i%2 == 0) ? 'even' : 'odd' ?>">
                                                    <label>
                                                        <input type="checkbox" value="<?php echo $Group->id; ?>" name="restrict_to[]" <?php echo set_checkbox('restrict_to[]', $Group->id, in_array($Group->id, $restrict_to) ? TRUE : FALSE); ?>>
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
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<?php js_start(); ?>
<script type="text/javascript">
    $(document).ready(function() {
        var editor = CodeMirror.fromTextArea(document.getElementById("layout"), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "application/x-httpd-php",
            indentUnit: 4,
            indentWithTabs: true,
            enterMode: "keep",
            tabMode: "shift"
        });

        var editor = CodeMirror.fromTextArea(document.getElementById("page_head"), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "application/x-httpd-php",
            indentUnit: 4,
            indentWithTabs: true,
            enterMode: "keep",
            tabMode: "shift"
        });

        $( "#tabs" ).tabs();

        // Save Content
        $("#save, #save_exit").click( function() {

            if ($('#category_group_id').val() != '<?php echo $Content_type->category_group_id; ?>')
            {
                if ( ! confirm('Changing category groups will delete current category entry relations of this content type.\n\n Are you sure you want to continue?'))
                {
                    return false;
                }
            }

            if ($(this).attr('id') == 'save_exit')
            {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'save_exit',
                    value: '1'
                }).appendTo('#layout_edit');

                $('#layout_edit').submit();
            }
            else
            {
                $('#layout_edit').submit();
            }
        });

        $('a.copy_text').zclip({
            path: '<?php echo theme_url('assets/js/zclip/ZeroClipboard.swf'); ?>',
            copy: function() {return $(this).text();},
            afterCopy: function() {}
        });

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
    });
</script>
<?php js_end(); ?>
