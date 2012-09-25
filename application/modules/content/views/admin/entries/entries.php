<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/review.png'); ?>"> Entries</h1>

        <div class="buttons">
            <ul id="add_entry_btn">
                <li id="add_entry_li">
                    <a class="button" rel="#entry_content_types" id="add_entry" href="javascript:void(0);"><span>Add Entry</span></a>
                    <ul id="content_types_dropdown">
                        <?php if ( ! empty($content_types_add_entry)): ?>
                            <?php foreach($content_types_add_entry as $content_type_id => $content_type_title): ?>
                                <li><a href="<?php echo site_url(ADMIN_PATH . '/content/entries/edit/' . $content_type_id); ?>"><?php echo $content_type_title; ?></a></li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li><div id="no_content_types_added">No content types added</div></li>
                        <?php endif; ?>
                    </ul>
                </li>
                <li>
                    <a class="button delete" href="#"><span>Delete</span></a>
                </li>
            </ul>
        </div>
    </div>
    <div class="content">
        <?php echo form_open(); ?>
        <div class="filter">
            <div class="left">
                <div><label>Search:</label></div> 
                <input type="text" name="filter[search]" value="<?php echo set_filter('entries', 'search'); ?>" /></td>
            </div>

            <div class="left">
                <div><label>Content Type:</label></div> 
                <?php echo form_dropdown('filter[content_type_id]', $content_types_filter, set_filter('entries', 'content_type_id'), 'style="min-width: 90px;"'); ?></td>
            </div>

            <div class="left">
                <div><label>Status:</label></div> 
                <?php echo form_dropdown('filter[status]', array(''=>'', 'published'=>'Published', 'draft'=>'Draft', 'disabled' => 'Disabled'), set_filter('entries', 'status')); ?></td>
            </div>
            
            <div class="left filter_buttons">
                <button name="submit" class="button" type="submit"><span>Filter</span></button>
                <button name="clear_filter" value="1" class="button" type="submit"><span>Clear</span></button>
            </div>
            <div class="clear"></div>
        </div>
        <?php echo form_close(); ?>

        <?php echo form_open(null, 'id="form"'); ?>
        <table id="entries_table" class="list">
            <thead>
                <tr>
                    <th width="1" class="center"><input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></th>
                    <th><a rel="title" class="sortable" href="#">Title</a></th>
                    <th><a rel="slug" class="sortable" href="#">URI</a></th>
                    <th class="right"><a rel="id" class="sortable" href="#">#ID</a></th>
                    <th><a rel="content_types_title" class="sortable" href="#">Content Type</a></th>
                    <th><a rel="status" class="sortable" href="#">Status</a></th>
                    <th><a rel="modified_date" class="sortable" href="#">Last Modified</a></th>
                    <th class="right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($Entries->exists()): ?>
                    <?php foreach($Entries as $Entry): ?>
                    <tr>
                        <td class="center"><input type="checkbox" value="<?php echo $Entry->id ?>" name="selected[]" /></td>
                        <td><?php echo $Entry->title; ?></td>
                        <td><?php echo ($Entry->slug) ? '/'. $Entry->slug : ''; ?></td>
                        <td class="right"><?php echo $Entry->id; ?></td>
                        <td><?php echo $Entry->content_types_title; ?></td>
                        <td><?php echo ucwords($Entry->status); ?></td>
                        <td><?php echo date('m/d/Y h:i a', strtotime($Entry->modified_date)); ?></td>
                        <td class="right"><?php if ($Entry->slug != ''): ?>[ <?php echo anchor("$Entry->slug", 'View', 'target="_blank"'); ?> ]<?php endif; ?> [ <?php echo anchor(ADMIN_PATH . "/content/entries/edit/" . $Entry->content_type_id . "/" . $Entry->id, 'Edit'); ?> ]</td>
                    </tr>
                    <? endforeach ?>
                <?php else: ?>
                    <tr class="center"><td colspan="8">No results found.</td></tr>
                <?php endif; ?>
            </tbody>
        </table>
        <?php echo form_close(); ?>
        <div class="pagination">
            <div class="links"><?php echo $this->pagination->create_links(); ?></div>
            <div class="results">Showing <?php echo ($Entries->exists()) ? $Entries->paged->current_row + 1 : 0; ?> to <?php echo $Entries->paged->current_row + $Entries->paged->items_on_page; ?> of <?php echo $Entries->paged->total_rows; ?> (<?php echo $Entries->paged->total_pages; ?>  Pages)</div>
        </div>
    </div>
</div>

<script type="text/javascript">

    // Sort By
    $('.sortable').click( function() {
        sort = $(this);

        if (sort.hasClass('asc'))
        {
            window.location.href = "<?php echo site_url(ADMIN_PATH . '/content/entries/index') . '?'; ?>&sort=" + sort.attr('rel') + "&order=desc";
        }
        else
        {
            window.location.href = "<?php echo site_url(ADMIN_PATH . '/content/entries/index') . '?';  ?>&sort=" + sort.attr('rel') + "&order=asc";
        }

        return false;
    });

    <?php if ($sort = $this->input->get('sort')): ?>
        $('a.sortable[rel="<?php echo $sort; ?>"]').addClass('<?php echo ($this->input->get('order')) ? $this->input->get('order') : 'asc' ?>');
    <?php else: ?>
        $('a.sortable[rel="modified_date"]').addClass('desc');
    <?php endif; ?>

    // Delete
    $('.delete').click( function() {
        if (confirm('Delete cannot be undone! Are you sure you want to do this?'))
        {
            $('#form').attr('action', '<?php echo site_url(ADMIN_PATH . '/content/entries/delete'); ?>').submit()
        }
        else
        {
            return false;
        }
    });

    $('#add_entry').click( function () {
        $('#content_types_dropdown').show();
        $('#add_entry').addClass('selected');
    });

    $(document).mouseup( function (e) {
        if ($('#content_types_dropdown').is(":visible") && $(e.target).parents('#add_entry_li').length == 0) {
            $('#add_entry').removeClass('selected');
            $('#content_types_dropdown').hide();
        }
    });
</script>
