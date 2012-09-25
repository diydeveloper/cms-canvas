<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/user.png'); ?>"> Users</h1>

        <div class="buttons">
            <a class="button" href="<?php echo site_url(ADMIN_PATH . "/users/edit"); ?>"><span>Add User</span></a>
            <a class="button delete" href="#"><span>Delete</span></a>
        </div>
    </div>
    <div class="content">
        <div class="filter">
            <form id="filter_form" method="post">
                <div class="left">
                    <div><label>Search:</label></div>
                    <?php echo  form_input(array('name'=>'filter[search]', 'class'=>'long', 'value' => set_filter('users', 'search'))) ?> 
                </div>

                <div class="left">
                    <div><label>Group:</label></div> 
                    <?php echo form_dropdown('filter[group_id]', option_array_value($Groups, 'id', 'name', array(''=>'')), set_filter('users', 'group_id')); ?></td>
                </div>

                <div class="left filter_buttons">
                    <button type="submit" class="button"><span>Filter</span></button>
                    <button type="submit" class="button" name="clear_filter" value="1"><span>Clear</span></button>
                </div>
            </form>
            <div class="clear"></div>
        </div>

        <?php echo form_open(null, 'id="form"'); ?>
            <table class="list">
                <thead>
                    <tr>
                        <th width="1" class="center"><input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></th>
                        <th><a rel="first_name" class="sortable" href="#">First Name</a></th>
                        <th><a rel="last_name" class="sortable" href="#">Last Name</a></th>
                        <th><a rel="email" class="sortable" href="#">Email</a></th>
                        <th><a rel="groups_name" class="sortable" href="#">Group</a></th>
                        <th><a rel="last_login" class="sortable" href="#">Last Login</a></th>
                        <th class="right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if ($Users->exists()): ?>
                        <?php foreach($Users as $User): ?>
                            <tr>
                                <td class="center"><input type="checkbox" value="<?php echo $User->id ?>" name="selected[]" /></td>
                                <td><?php echo $User->first_name ?></td>
                                <td><?php echo $User->last_name ?></td>
                                <td><?php echo $User->email ?></td>
                                <td><?php echo $User->groups_name ?></td>
                                <td><?php echo (empty($User->last_login)) ? '' : date('M j, Y h:i a', strtotime($User->last_login))?></td>
                                <td class="right">[ <?php echo anchor(ADMIN_PATH . '/users/login-as-user/' . $User->id, 'Login')?> ] [ <?php echo anchor(ADMIN_PATH . '/users/edit/' . $User->id, 'Edit')?> ]</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="center">No results found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php form_close(); ?>

        <div class="pagination">
            <div class="links"><?php echo $this->pagination->create_links(); ?></div>
            <div class="results">Showing <?php echo ($Users->exists()) ? $Users->paged->current_row + 1 : 0; ?> to <?php echo $Users->paged->current_row + $Users->paged->items_on_page; ?> of <?php echo $Users->paged->total_rows; ?> (<?php echo $Users->paged->total_pages; ?>  Pages)</div>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready( function() {
        $('.sortable').click( function() {
            sort = $(this);

            if (sort.hasClass('asc'))
            {
                window.location.href = "<?php echo site_url(ADMIN_PATH . '/users/index') . '?search=' . $this->input->get('search') . '&group_id=' . $this->input->get('group_id'); ?>&sort=" + sort.attr('rel') + "&order=desc";
            }
            else
            {
                window.location.href = "<?php echo site_url(ADMIN_PATH . '/users/index') . '?search=' . $this->input->get('search') . '&group_id=' . $this->input->get('group_id'); ?>&sort=" + sort.attr('rel') + "&order=asc";
            }

            return false;
        });

        <?php if ($sort = $this->input->get('sort')): ?>
            $('a.sortable[rel="<?php echo $sort; ?>"]').addClass('<?php echo ($this->input->get('order')) ? $this->input->get('order') : 'asc' ?>');
        <?php else: ?>
            $('a.sortable[rel="last_name"]').addClass('asc');
        <?php endif; ?>

        $('.delete').click( function() {
            if (confirm('Delete cannot be undone! Are you sure you want to do this?'))
            {
                $('#form').attr('action', '<?php echo site_url(ADMIN_PATH . '/users/delete'); ?>').submit()
            }
            else
            {
                return false;
            }
        });
    });
</script>
