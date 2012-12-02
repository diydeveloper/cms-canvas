<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/banner.png'); ?>"> Image Galleries</h1>

        <div class="buttons">
            <a class="button" href="<?php echo site_url(ADMIN_PATH . "/galleries/edit"); ?>"><span>Add Gallery</span></a>
            <a class="button delete" href="#"><span>Delete</span></a>
        </div>
    </div>
    <div class="content">

        <?php echo form_open(null, 'id="form"'); ?>
        <table class="list">
            <thead>
                <tr>
                    <th width="1" class="center"><input type="checkbox" onClick="$('input[name*=\'selected\']').attr('checked', this.checked);" /></th>
                    <th class="sortable">Title</th>
                    <th class="right">#ID</th>
                    <th class="right">Action</th>
                </tr>
            </thead>
            <tbody>
                <?php if ($Galleries->exists()): ?>
                    <?php foreach($Galleries as $Gallery):?>
                    <tr>
                        <td class="center"><input type="checkbox" value="<?php echo $Gallery->id ?>" name="selected[]" /></td>
                        <td><?php echo $Gallery->title; ?></td>
                        <td class="right"><?php echo $Gallery->id; ?></td>
                        <td class="right">[ <a href="<?php echo site_url(ADMIN_PATH . '/galleries/edit/' . $Gallery->id) ?>">Rename</a> ] [ <a href="<?php echo site_url(ADMIN_PATH . '/galleries/images/index/' . $Gallery->id) ?>">Edit</a> ]</td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td class="center" colspan="4">No galleries have been added.</td>
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

        $('.delete').click(function() {
            if (confirm('Delete cannot be undone! Are you sure you want to do this?\n\n NOTE: Images will remain on the server.'))
            {
                $('#form').attr('action', '<?php echo site_url(ADMIN_PATH . '/galleries/delete'); ?>').submit()
            }
            else
            {
                return false;
            }
        });

    });
</script>
<?php js_end(); ?>