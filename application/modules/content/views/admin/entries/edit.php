<!--[if lte IE 7]> <style type="text/css"> #entry_fields > div > label .arrow { display: inline; } </style> <![endif]-->

<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/review.png'); ?>"> Entry Edit <?php echo ($edit_mode) ? '- ' . strip_tags($Entry->title) . ' (#' . $Entry->id . ')' : ''; ?></h1>

        <div class="buttons">
            <a class="button" href="javascript:void(0);" id="save"><span>Save</span></a>
            <a class="button" href="javascript:void(0);" id="save_exit"><span>Save &amp; Exit</span></a>
            <a class="button" href="<?php echo site_url(ADMIN_PATH . '/content/entries'); ?>"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">

        <?php if ($edit_mode && $Entry->slug != ''): ?>
            <a style="float: right; z-index: 1; position: relative;" target="_blank" href="<?php echo site_url("$Entry->slug"); ?>"><img src="<?php echo theme_url('assets/images/preview-icon-medium.png') ?>" /></a>
        <?php endif; ?>

        <div class="fright" style="margin-top: 4px; margin-right: 10px;">
            <a id="collapse_all" class="no_underline" href="javascript:void(0);">Collapse All</a> &nbsp;|&nbsp; <a id="expand_all" class="no_underline" href="javascript:void(0);">Expand All</a>
        </div>

        <?php echo form_open(null, 'id="entry_edit"'); ?>
        <div class="tabs">
            <ul class="htabs">
                <li><a href="#content-tab">Content</a></li>
                <?php if ($Content_type->category_group_id): ?>
                    <li><a href="#categories-tab">Categories</a></li>
                <?php endif; ?>
                <li><a href="#page-tab">Page</a></li>
                <?php if ($Content_type->enable_versioning): ?>
                    <li><a href="#revisions-tab">Revisions</a></li>
                <?php endif; ?>
                <li><a href="#settings-tab">Settings</a></li>
            </ul>
            <!-- Content Tab -->
            <div id="content-tab">
                <div id="entry_fields">
                    <div>
                        <?php echo form_label('<div class="arrow arrow_expand"></div><span class="required">*</span> Title', 'title'); ?>
                        <div>
                            <?php echo form_input(array('name'=>'title', 'id'=>'title', 'value'=>set_value('title', !empty($Entry->title) ? $Entry->title : ''))); ?>
                        </div>
                    </div>

                    <?php if ($Content_type->dynamic_route != ''): ?>
                        <div>
                            <?php echo form_label('<div class="arrow arrow_expand"></div><span class="required">*</span> URL Title', 'url_title'); ?>
                            <div>
                                <?php echo form_input(array('name'=>'url_title', 'id'=>'url_title', 'value'=>set_value('url_title', !empty($Entry->url_title) ? $Entry->url_title : ''))); ?>
                            </div>
                        </div>
                    <?php endif; ?>

                    <?php if ( ! empty($Fields)): ?>
                        <?php echo $Fields; ?>
                    <?php endif; ?>
                </div>
            </div>
            <!-- Categories Tab -->
            <?php if ($Content_type->category_group_id): ?>
            <div id="categories-tab">
                <div class="form format_list">
                    <?php echo $categories_tree; ?>        
                </div>
            </div>
            <?php endif; ?>
            <!-- Page Tab -->
            <div id="page-tab">
                <div class="form">
                    <div>
                        <?php echo form_label('URL:', 'slug'); ?>
                        <span style="line-height: 24px; "> <?php echo trim(site_url(), '/'); ?>/ </span>
                        <?php echo form_input(array('name'=>'slug', 'id'=>'slug', 'value'=>set_value('slug', !empty($Entry->slug) ? $Entry->slug : ''))); ?>
                    </div>
                    <div>
                        <?php echo form_label('Meta Title:<br /><span class="help">65 Characters Max</span>', 'meta_title'); ?>
                        <?php echo form_input(array('name'=>'meta_title', 'id'=>'meta_title', 'class'=>'long', 'value'=>set_value('meta_title', !empty($Entry->meta_title) ? $Entry->meta_title : ''))); ?>
                        &nbsp;<span id="meta_title_count" class="help" style="display: inline;">(<?php echo strlen(set_value('meta_title', !empty($Entry->meta_title) ? $Entry->meta_title : '')); ?> Chars)</span>
                    </div>
                    <div>
                        <?php echo form_label('Keywords:<span class="help">250 Characters Max</span>', 'meta_keywords'); ?>
                        <?php echo form_textarea(array('name'=>'meta_keywords', 'id'=>'meta_keywords', 'style'=>'height: 50px;','value'=>set_value('meta_keywords', !empty($Entry->meta_keywords) ? $Entry->meta_keywords : ''))); ?>
                        &nbsp;<span id="meta_keywords_count" class="help" style="display: inline;">(<?php echo strlen(set_value('meta_keywords', !empty($Entry->meta_keywords) ? $Entry->meta_keywords : '')); ?> Chars)</span>
                    </div>
                    <div>
                        <?php echo form_label('Description:<br /><span class="help">150 Characters Max</span>', 'meta_description'); ?>
                        <?php echo form_textarea(array('name'=>'meta_description', 'id'=>'description_textarea', 'value'=>set_value('meta_description', !empty($Entry->meta_description) ? $Entry->meta_description : ''))); ?>
                        &nbsp;<span id="meta_description_count" class="help" style="display: inline;">(<?php echo strlen(set_value('meta_description', !empty($Entry->meta_description) ? $Entry->meta_description : '')); ?> Chars)</span>
                    </div>
                </div>
            </div>
            <!-- Revisions Tab -->
            <?php if ($Content_type->enable_versioning): ?>
            <div id="revisions-tab">
                <?php $Entry_revisions = $Entry->get_entry_revisions(); $r = $Entry_revisions->result_count(); ?>
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
                        <?php if ($Entry_revisions->exists()): ?>
                            <?php foreach($Entry_revisions as $Revision): ?>
                                <tr>
                                    <td>Revision <?php echo $r; ?></td>
                                    <td><?php echo $Revision->author_name; ?></td>
                                    <td><?php echo date('m/d/Y h:i a', strtotime($Revision->revision_date)); ?></td>
                                    <td class="right">
                                        <?php if ( ($revision_id == '' && $r == $Entry_revisions->result_count()) 
                                            || $Revision->id == $revision_id): ?>
                                            <strong>Currently Loaded</strong>
                                        <?php else: ?>
                                            [ <a href="<?php echo site_url(ADMIN_PATH . '/content/entries/edit/' . $Revision->content_type_id . '/' . $Revision->resource_id . '/' . $Revision->id); ?> ">Load Revision</a> ]</td>
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
            <?php endif; ?>
            <!-- Settings Tab -->
            <div id="settings-tab">
                <div class="form">
                    <div>
                        <?php echo form_label('<span class="required">*</span> Status:', 'status'); ?>
                        <?php echo form_dropdown('status', array('published'=>'Published', 'draft'=>'Draft', 'disabled' => 'Disabled'), set_value('status', !empty($Entry->status) ? $Entry->status : ''), 'id=\'status\'')?>
                    </div>
                    <div>
                        <?php echo form_label('<span class="required">*</span> Date Created:', 'created_date'); ?>
                        <?php echo form_input(array('name'=>'created_date', 'class'=>'datetime', 'id'=>'created_date', 'value'=>set_value('created_date', !empty($Entry->created_date) ? date('m/d/Y h:i:s a', strtotime($Entry->created_date)) : date('m/d/Y h:i:s a')))); ?>
                    </div>
                    <div>
                        <?php echo form_label('Author:', 'author_id'); ?>
                        <?php if ($edit_mode): ?>
                            <?php echo form_dropdown('author_id', $authors, set_value('author_id', !empty($Entry->author_id) ? $Entry->author_id : ''), 'id=\'author_id\'')?>
                        <?php else: ?>
                            <?php echo form_dropdown('author_id', $authors, $this->secure->get_user_session()->id, 'id=\'author_id\'')?>
                        <?php endif; ?>
                    </div>
                    <div>
                        <?php echo form_label('Content Type Change:', 'content_type_id'); ?>
                        <?php echo form_dropdown('content_type_change', $change_content_types, '', 'id="content_type_change"'); ?>
                        <a class="ex" id="load_content_type"; href="javascript:void(0);">Load</a>
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
        $( ".tabs" ).tabs();

        $( ".datetime" ).datetimepicker({
            showSecond: true,
            timeFormat: 'hh:mm:ss tt',
            ampm: true
        });

        // Wrap datepicker popup with a class smoothness for styleing
        $('body').find('#ui-datepicker-div').wrap('<div class="smoothness"></div>');

        $("#load_content_type").click( function() {

            if ($('#content_type_change').val() == '')
            {
                alert('No content type was selected.');
            }
            else
            {
                response = confirm('Changing the content type will only carry over content from fields with matching short tags in both content types.\nAny current unsaved data will be lost.\n\n Are you sure you want to continue?');

                if (response)
                {
                    window.location = "<?php echo site_url(ADMIN_PATH . '/content/entries/edit'); ?>/" + $('#content_type_change').val() + "/<?php echo $Entry->id; ?>/convert";
                }
            }
        });

        $("#save, #save_exit").click( function() {

            response = true;

            if ($('#status').val() != '<?php echo empty($Entry->status) ? 'published' : $Entry->status; ?>' && $('#status').val() != 'published')
            {
                response = confirm('When changing the page type from published ensure you do not have any published navigations or links to this page.\n\n Are you sure you want to continue?');
            }

            if (response)
            {
                if ($(this).attr('id') == 'save_exit')
                {
                    $('<input>').attr({
                        type: 'hidden',
                        name: 'save_exit',
                        value: '1'
                    }).appendTo('#entry_edit');

                    $('#entry_edit').submit();
                }
                else
                {
                    $('#entry_edit').submit();
                }
            }
        });

        // Count meta title characters
        $('#meta_title').keyup( function() {
            $('#meta_title_count').html('(' + $(this).val().length + ' Chars)');
        });

        // Count keyword characters
        $('#meta_keywords').keyup( function() {
            $('#meta_keywords_count').html('(' + $(this).val().length + ' Chars)');
        });

        // Count description characters
        $('#description_textarea').keyup( function() {
            $('#meta_description_count').html('(' + $(this).val().length + ' Chars)');
        });

        // Expand / Collapse entry fields
        $('#entry_fields > div > label').click( function() {
            if($(this).next('div').is(":visible"))
            {
                $(this).next('div').slideUp();
                $('div', this).removeClass('arrow_expand').addClass('arrow_collapse');
            }
            else
            {
                $(this).next('div').slideDown();
                $('div', this).removeClass('arrow_collapse').addClass('arrow_expand');
            }
        });

        <?php if ( ! $edit_mode): ?>
            // Auto Generate Url Title
            $('#title').keyup( function(e) {
                $('#url_title').val($(this).val().toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-_]/g, ''))
            });
        <?php endif; ?>

        heading_pos = $('.heading').offset().top;
        position_top = false;

        $(window).scroll(function () {
            if (heading_pos - $(window).scrollTop() <= 0) {
                if (!position_top) {
                    $('.heading').addClass('position_top');
                    $('.content').addClass('position_top');
                    position_top = true;
                }
            } else {
                if (position_top) {
                    $('.heading').removeClass('position_top');
                    $('.content').removeClass('position_top');
                    position_top = false;
                }
            }
        });

        $('#collapse_all').click( function() {
            $('.arrow_expand').trigger('click');
        });

        $('#expand_all').click( function() {
            $('.arrow_collapse').trigger('click');
        });
    });
</script>
<?php js_end(); ?>
