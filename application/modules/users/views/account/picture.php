<div style="float: left; width: 200px;">
    <img <?php echo ($User->profile_picture_exists()) ? 'class="frame"' : ''; ?> src="<?php echo $User->profile_picture(); ?>" /><br />
    <?php if ($User->profile_picture_exists()): ?>
        <a id="remove_picture" class="edit" href="<?php echo site_url('/users/account/remove-picture'); ?>">Remove Your Picture</a>
    <?php endif; ?>
</div>

<div style="float: left;">
    <h2>Upload Image</h2><br />
    <?php echo form_open_multipart(); ?>
    <div>
        <div>Allowed filetypes: jpg, gif, png</div>
        <input name="userfile" type="file" size="35" />
        <input name="hidden_value" value="1" type="hidden" />
    </div>
    <div>
        <input class="submit" type="submit" value="Upload" />
    </div>
    <?php echo form_close(); ?>
</div>


<script type="text/javascript">
    $(document).ready( function() {
        $('#remove_picture').click( function() {

            var response = confirm('Are you sure you want to remove this image?');

            if(response)
            {
                return true;
            }
            else
            {
                return false;
            }
        });
    });
</script>
