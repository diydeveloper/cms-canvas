<?php $this->load->view('content/admin/field_types_subnav'); ?>

<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/layout.png'); ?>">Content Fields - <?php echo $Type->title; ?> (<?php echo $Type->short_name; ?>)</h1>

        <div class="buttons">
            <a class="button" href="<?php echo site_url(ADMIN_PATH . "/content/fields/edit/" . $Type->id); ?>"><span>Add Field</span></a>
            <a class="button delete" href="#"><span>Delete</span></a>
        </div>
    </div>
    <div class="content">

        <?php echo form_open(null, 'id="form"'); ?>
            <table id="fields_table" class="list">
                <thead>
                    <tr class="nodrag nodrop">
                        <th style="width: 10px;"></th>
                        <th width="1" class="center"><input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></th>
                        <th>Title</th>
                        <th>Short Tag</th>
                        <th>Type</th>
                        <th class="right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($Fields->exists()): ?>
                        <?php foreach($Fields as $Field): ?>
                        <tr id="<?php echo $Field->id ?>">
                            <td class="drag_handle"></td>
                            <td class="center"><input type="checkbox" value="<?php echo $Field->id ?>" name="selected[]" /></td>
                            <td><?php echo $Field->label; ?></td>
                            <td>{{ <?php echo $Field->short_tag; ?> }}</td>
                            <td><?php echo $Field->content_field_types_title; ?></td>
                            <td class="right">[ <a href="<?php echo site_url(ADMIN_PATH . '/content/fields/edit/' . $Type->id . '/' . $Field->id); ?>">Edit</a> ]</td>
                        </tr>
                        <?endforeach?>
                    <?php else: ?>
                        <tr>
                            <td class="center" colspan="6">No content fields have been added.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php echo form_close(); ?>

    </div>
</div>

<?php js_start(); ?>
<script type="text/javascript">
    $(document).ready(function() {

        $('.delete').click( function() {
            if (confirm('Delete cannot be undone! Are you sure you want to do this?'))
            {
                $('#form').attr('action', '<?php echo site_url(ADMIN_PATH . '/content/fields/delete/' . $Type->id); ?>').submit()
            }
            else
            {
                return false;
            }
        });

        // Sort fields (table sort)
        $('#fields_table').tableDnD({
            onDrop: function(table, row) {
                show_status('Saving...', false, true);
                order = $('#fields_table').tableDnDSerialize()
                $.post('<?php echo site_url(ADMIN_PATH . '/content/fields/order') ?>', order , function() {
                    show_status('Saved', true, false);
                });
            },
            dragHandle: "drag_handle"
        });

    });
</script>
<?php js_end(); ?>
