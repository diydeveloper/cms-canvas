<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/setting.png'); ?>"> Clear Cached Data</h1>

        <div class="buttons">
            <a class="button" href="#" onClick="$('#cache_form').submit();"><span>Clear</span></a>
        </div>
    </div>
    <div class="content">

        <div class="form">
            <?php echo form_open(null, 'id="cache_form"'); ?>
                <div>
                    <span>
                        <label><?php echo form_checkbox(array('name' => 'cache[settings]', 'value' => '1', 'checked' => set_checkbox('cache[settings]', '1', TRUE))); ?> Settings</label>
                    </span>
                </div> 
                <div>
                    <span>
                        <label><?php echo form_checkbox(array('name' => 'cache[entries]', 'value' => '1', 'checked' => set_checkbox('cache[entries]', '1', TRUE))); ?> Entries</label>
                    </span>
                </div> 
                <div>
                    <span>
                        <label><?php echo form_checkbox(array('name' => 'cache[content_types]', 'value' => '1', 'checked' => set_checkbox('cache[content_types]', '1', TRUE))); ?> Content Types</label>
                    </span>
                </div>
                <div>
                    <span>
                        <label><?php echo form_checkbox(array('name' => 'cache[snippets]', 'value' => '1', 'checked' => set_checkbox('cache[snippets]', '1', TRUE))); ?> Code Snippets</label>
                    </span>
                </div>
                <div>
                    <span>
                        <label><?php echo form_checkbox(array('name' => 'cache[navigations]', 'value' => '1', 'checked' => set_checkbox('cache[navigations]', '1', TRUE))); ?> Navigations</label>
                    </span>
                </div>
                <div>
                    <span>
                        <label><?php echo form_checkbox(array('name' => 'cache[categories]', 'value' => '1', 'checked' => set_checkbox('cache[categories]', '1', TRUE))); ?> Categories</label>
                    </span>
                </div>
                <div>
                    <span>
                        <label><?php echo form_checkbox(array('name' => 'cache[images]', 'value' => '1', 'checked' => set_checkbox('cache[images]', '1', TRUE))); ?> Images</label>
                    </span>
                </div>
                <div>
                    <span>
                        <label><?php echo form_checkbox(array('name' => 'cache[datamapper]', 'value' => '1', 'checked' => set_checkbox('cache[datamapper]', '1', TRUE))); ?> DB Schema</label>
                    </span>
                </div>
            <?php echo form_close(); ?>
        </div>

    </div>
</div>
