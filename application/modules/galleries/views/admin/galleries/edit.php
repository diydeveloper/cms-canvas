<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/banner.png'); ?>"> <?php echo ($edit_mode) ? 'Edit' : 'Add' ?> Gallery</h1>

        <div class="buttons">
            <a class="button" href="#" onClick="$('#galleries_form').submit();"><span>Save</span></a>
        </div>
    </div>
    <div class="content">

        <?php echo form_open(null, 'id="galleries_form"')?>

        <div class="form">
            <div>
                <?php echo form_label('Title:', 'title')?>
                <?php echo form_input(array('name' => 'title', 'value' => set_value('title', isset($Gallery->title) ? $Gallery->title : '')))?>
            </div>
        </div>

        <div class="clear"></div>

        <?php echo form_close(); ?>
    </div>
</div>
