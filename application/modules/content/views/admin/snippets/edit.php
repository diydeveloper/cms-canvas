<div class="box">
    <div class="heading">
        <h1>
            <img alt="" src="<?php echo theme_url('assets/images/layout.png'); ?>">Snippet 
            <?php if ( ! $edit_mode): ?>
                Add
            <?php else: ?>
                Edit - <?php echo $Snippet->title; ?> (<?php echo $Snippet->short_name; ?>)
            <?php endif; ?>
        </h1>

        <div class="buttons">
            <a class="button" href="javascript:void(0);" id="save"><span>Save</span></a>
            <a class="button" href="javascript:void(0);" id="save_exit"><span>Save &amp; Exit</span></a>
            <a class="button" href="<?php echo site_url(ADMIN_PATH . '/content/snippets'); ?>"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">

        <?php echo form_open('', 'id="snippet_edit"'); ?>
        <div class="form">
            <div>
                <?php echo form_label('<span class="required">*</span> Title', 'title'); ?>
                <?php echo form_input(array('name' => 'title', 'id' => 'title', 'value' => set_value('snippet', !empty($Snippet->title) ? $Snippet->title : ''))); ?>
            </div>
            <div>
                <?php echo form_label('<span class="required">*</span> Short Name:', 'short_name'); ?>
                <?php echo form_input(array('name' => 'short_name', 'id' => 'short_name', 'value' => set_value('snippet', !empty($Snippet->short_name) ? $Snippet->short_name : ''))); ?>
            </div>
        </div>
        <br />
        <div>
            <div id="tabs">
                <ul class="htabs">
                    <li><a href="#snippet-tab">Snippet</a></li>
                    <li><a href="#revisions-tab">Revisions</a></li>
                </ul>
                <div id="snippet-tab">
                    <?php echo form_textarea(array('name'=>'snippet', 'id'=>'snippet', 'value'=>set_value('snippet', !empty($Snippet->snippet) ? $Snippet->snippet : ''))); ?>
                </div>
                <div id="revisions-tab">
                    <?php $Revisions = $Snippet->get_revisions(); $r = $Revisions->result_count(); ?>
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
                            <?php if ($Revisions->exists()): ?>
                                <?php foreach($Revisions as $Revision): ?>
                                    <tr>
                                        <td>Revision <?php echo $r; ?></td>
                                        <td><?php echo $Revision->author_name; ?></td>
                                        <td><?php echo date('m/d/Y h:i a', strtotime($Revision->revision_date)); ?></td>
                                        <td class="right">
                                            <?php if ( ($revision_id == '' && $r == $Revisions->result_count()) 
                                                || $Revision->id == $revision_id): ?>
                                                <strong>Currently Loaded</strong>
                                            <?php else: ?>
                                                [ <a href="<?php echo site_url(ADMIN_PATH . '/content/snippets/edit/' . $Revision->resource_id . '/' . $Revision->id); ?> ">Load Revision</a> ]</td>
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
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<?php js_start(); ?>
<script type="text/javascript">
    $(document).ready(function() {
        var editor = CodeMirror.fromTextArea(document.getElementById("snippet"), {
            lineNumbers: true,
            matchBrackets: true,
            mode: "application/x-httpd-php",
            indentUnit: 4,
            indentWithTabs: true,
            enterMode: "keep",
            tabMode: "shift"
        });

        $( "#tabs" ).tabs();

        <?php if ( ! $edit_mode): ?>
            // Auto Generate Url Title
            $('#title').keyup( function(e) {
                $('#short_name').val($(this).val().toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-_]/g, ''))
            });
        <?php endif; ?>

        // Save Content
        $("#save, #save_exit").click( function() {
            if ($(this).attr('id') == 'save_exit')
            {
                $('<input>').attr({
                    type: 'hidden',
                    name: 'save_exit',
                    value: '1'
                }).appendTo('#snippet_edit');

                $('#snippet_edit').submit();
            }
            else
            {
                $('#snippet_edit').submit();
            }
        });
    });
</script>
<?php js_end(); ?>
