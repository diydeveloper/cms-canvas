<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/user-group.png'); ?>"> User Groups</h1>

        <div class="buttons">
            <a class="button" href="<?php echo site_url(ADMIN_PATH . "/users/groups/edit"); ?>"><span>Add Group</span></a>
            <a class="button delete" href="#"><span>Delete</span></a>
        </div>
    </div>
    <div class="content">
        <?php echo form_open(null, 'id="form"'); ?>
            <table class="list">
                <thead>
                    <tr>
                        <th width="1" class="center"><input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></th>
                        <th><a rel="name" class="sortable" href="#">Name</a></th>
                        <th class="right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($Groups->exists()): ?>
                        <?php foreach($Groups as $Group): ?>
                            <tr>
                                <td class="center"><input type="checkbox" value="<?php echo $Group->id ?>" name="selected[]" /></td>
                                <td><?php echo $Group->name ?></td>
                                <td class="right">[ <?php echo anchor(ADMIN_PATH . '/users/groups/edit/' . $Group->id, 'Edit')?> ]</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">No results found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php form_close(); ?>

        <div class="pagination">
            <div class="links"><?php echo $this->pagination->create_links(); ?></div>
            <div class="results">Showing <?php echo ($Groups->exists()) ? $Groups->paged->current_row + 1 : 0; ?> to <?php echo $Groups->paged->current_row + $Groups->paged->items_on_page; ?> of <?php echo $Groups->paged->total_rows; ?> (<?php echo $Groups->paged->total_pages; ?>  Pages)</div>
        </div>
    </div>
</div>

<?php js_start(); ?>
<script type="text/javascript">
    $(document).ready( function() {
        $('.sortable').click( function() {
            sort = $(this);

            if (sort.hasClass('asc'))
            {
                window.location.href = "<?php echo site_url(ADMIN_PATH . '/users/groups/index') . '?search=' . $this->input->get('search'); ?>&sort=" + sort.attr('rel') + "&order=desc";
            }
            else
            {
                window.location.href = "<?php echo site_url(ADMIN_PATH . '/users/groups/index') . '?search=' . $this->input->get('search'); ?>&sort=" + sort.attr('rel') + "&order=asc";
            }

            return false;
        });

        <?php if ($sort = $this->input->get('sort')): ?>
            $('a.sortable[rel="<?php echo $sort; ?>"]').addClass('<?php echo ($this->input->get('order')) ? $this->input->get('order') : 'asc' ?>');
        <?php else: ?>
            $('a.sortable[rel="name"]').addClass('asc');
        <?php endif; ?>

        $('.delete').click( function() {
            if (confirm('Delete cannot be undone! Are you sure you want to do this?'))
            {
                $('#form').attr('action', '/admin/users/groups/delete').submit()
            }
            else
            {
                return false;
            }
        });
    });
</script>
<?php js_end(); ?>