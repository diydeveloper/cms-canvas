<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/category.png'); ?>"> Navigations</h1>

        <div class="buttons">
            <a class="button" href="<?php echo site_url(ADMIN_PATH . "/navigations/edit"); ?>"><span>Add Navigation</span></a>
            <a class="button delete" href="#"><span>Delete</span></a>
        </div>
    </div>
    <div class="content">
        <?php echo form_open(null, 'id="form"')?>
            <table id="groups_table" class="list">
                <thead>
                    <tr>
                        <th width="1" class="center"><input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></th>
                        <th><a rel="title" class="sortable" href="#">Title</a></th>
                        <th class="right"><a rel="id" class="sortable" href="#">#ID</a></th>
                        <th class="right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($Groups->exists()): ?>
                        <?php foreach($Groups as $Group): ?>
                            <tr>
                                <td class="center"><input type="checkbox" value="<?php echo $Group->id ?>" name="selected[]" /></td>
                                <td><?php echo $Group->title; ?></td>
                                <td class="right"><?php echo $Group->id; ?></td>
                                <td class="right">
                                    [ <a class="rename" href="<?php echo site_url(ADMIN_PATH . '/navigations/edit/' . $Group->id); ?>">Rename</a> ]
                                    [ <a href="<?php echo site_url(ADMIN_PATH . "/navigations/items/tree/$Group->id"); ?>">Edit</a> ]
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="4">There are currently no navigations.</td>
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
                window.location.href = "<?php echo site_url(ADMIN_PATH . '/navigations/index') . '?'; ?>&sort=" + sort.attr('rel') + "&order=desc";
            }
            else
            {
                window.location.href = "<?php echo site_url(ADMIN_PATH . '/navigations/index') . '?';  ?>&sort=" + sort.attr('rel') + "&order=asc";
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
                $('#form').attr('action', '<?php echo site_url(ADMIN_PATH . '/navigations/delete'); ?>').submit()
            }
            else
            {
                return false;
            }
        });

    });
</script>
<?php js_end(); ?>