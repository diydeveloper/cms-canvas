<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/banner.png'); ?>"> <?php echo $Gallery->title; ?> (#<?php echo $Gallery->id; ?>) &ndash; Images</h1>

        <div class="buttons">
            <a class="button" id="add_image" href="javascript:void(0);"><span>Add Images</span></a>
            <a class="button delete" href="#"><span>Delete</span></a>
        </div>
    </div>
    <div class="content">

        <?php echo form_open(null, 'id=form'); ?>
        <table id="image_table" class="list">
            <thead>
                <tr class="nodrag nodrop">
                    <th style="width: 10px;"></th>
                    <th width="1" class="center"><input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></th>
                    <th width="50%">Title</th>
                    <th>Image</th>
                    <th class="right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($Images->exists()): ?>
                    <?php foreach($Images as $Image):?>
                    <tr id="<?php echo $Image->id; ?>">
                        <td class="drag_handle"></td>
                        <td class="center"><input type="checkbox" value="<?php echo $Image->id; ?>" name="selected[]" /></td>
                        <td><?php echo $Image->title; ?> <?php if($Image->hide): ?> (Hidden)<?php endif; ?></td>
                        <td><img src="<?php echo image_thumb($Image->filename, 50, 50, true); ?>" /></td>
                        <td class="right">[ <a href="<?php echo site_url(ADMIN_PATH . '/galleries/images/edit/'.$Image->id); ?>">Edit</a> ]</td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="5" class="center">No images have been added.</td>
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

        initDnD = function() {
            // Sort images (table sort)
            $('#image_table').tableDnD({
                onDrop: function(table, row) {
                    show_status('Saving...', false, true);
                    order = $('#image_table').tableDnDSerialize()
                    $.post('<?php echo site_url(ADMIN_PATH . '/galleries/images/order') ?>', order, function() {
                        show_status('Saved', true, false);
                    } );
                },
                dragHandle: "drag_handle"
            });
        }

        // Delete
        $('.delete').click( function() {
            if (confirm('Delete cannot be undone! Images will remain on the server. Are you sure you want to do this?'))
            {
                $('#form').attr('action', '<?php echo site_url(ADMIN_PATH . '/galleries/images/delete/' . $Gallery->id); ?>').submit()
            }
            else
            {
                return false;
            }
        });

        initDnD();

        // KCFinder add images
        $('#add_image').click( function() {
            window.KCFinder = {
                callBackMultiple: function(files) {
                    window.KCFinder = null;
                    $.post('<?php echo site_url(ADMIN_PATH . '/galleries/images/add'); ?>', {'files': files, 'gallery_id': <?php echo $Gallery->id; ?>}, function(files) {
                        // Refresh image table
                        $('#image_table').load("<?php echo current_url(); ?> #image_table > *", function(){ initDnD(); });
                    });
                }
            };
            var left = (screen.width/2)-(800/2);
            var top = (screen.height/2)-(600/2);
            window.open('/assets/js/kcfinder/browse.php?type=images',
                'kcfinder_image', 'status=0, toolbar=0, location=0, menubar=0, ' +
                'directories=0, resizable=1, scrollbars=0, width=800, height=600, top=' + top + ', left=' + left
            );
        });


    });
</script>
<?php js_end(); ?>
