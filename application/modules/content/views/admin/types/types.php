<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/layout.png'); ?>">Content Types</h1>

        <div class="buttons">
            <a class="button" href="<?php echo site_url(ADMIN_PATH . "/content/types/add"); ?>"><span>Add Content Type</span></a>
            <a class="button delete" href="#"><span>Delete</span></a>
        </div>
    </div>
    <div class="content">

        <?php echo form_open(null, 'id="form"'); ?>
            <table class="list">
                <thead>
                    <tr>
                        <th width="1" class="center"><input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></th>
                        <th><a rel="title" class="sortable" href="#">Title</a></th>
                        <th><a rel="short_name" class="sortable" href="#">Short Name</a></th>
                        <th class="right">Entry Count</th>
                        <th class="right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($Types->exists()): ?>
                        <?php foreach($Types as $Type): ?>
                        <tr class="tr_link">
                            <td class="center"><input type="checkbox" value="<?php echo $Type->id ?>" name="selected[]" /></td>
                            <td><?php echo $Type->title; ?></td>
                            <td><?php echo $Type->short_name; ?></td>
                            <td class="right"><?php echo $Type->entries->count() . ' / ' . (($Type->entries_allowed == '') ? 'Unlimited' : $Type->entries_allowed); ?></td>
                            <td class="right">[ <a href="<?php echo site_url(ADMIN_PATH . '/content/fields/index/' . $Type->id); ?>">Fields</a> ] [ <a href="<?php echo site_url(ADMIN_PATH . '/content/types/edit/' . $Type->id); ?>">Edit</a> ]</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td class="center" colspan="4">No content types have been added.</td>
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
        // Sort By
        $('.sortable').click( function() {
            sort = $(this);

            if (sort.hasClass('asc'))
            {
                window.location.href = "<?php echo site_url(ADMIN_PATH . '/content/types/index') . '?'; ?>&sort=" + sort.attr('rel') + "&order=desc";
            }
            else
            {
                window.location.href = "<?php echo site_url(ADMIN_PATH . '/content/types/index') . '?';  ?>&sort=" + sort.attr('rel') + "&order=asc";
            }

            return false;
        });

        <?php if ($sort = $this->input->get('sort')): ?>
            $('a.sortable[rel="<?php echo $sort; ?>"]').addClass('<?php echo ($this->input->get('order')) ? $this->input->get('order') : 'asc' ?>');
        <?php else: ?>
            $('a.sortable[rel="title"]').addClass('asc');
        <?php endif; ?>

        $('.delete').click( function() {
            if (confirm('Delete cannot be undone! Are you sure you want to do this?'))
            {
                $('#form').attr('action', '<?php echo site_url(ADMIN_PATH . '/content/types/delete'); ?>').submit()
            }
            else
            {
                return false;
            }
        });

    });
</script>
<?php js_end(); ?>
