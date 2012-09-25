<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/category.png'); ?>"> Category Edit</h1>

        <div class="buttons">
            <a class="button" href="#" onClick="$('#form').submit()"><span>Save</span></a>
            <a class="button" href="<?php echo site_url(ADMIN_PATH . '/content/categories/tree/'. $Group->id); ?>"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">

        <?php echo form_open(null, 'id="form"'); ?>
        <div id="tabs">
            <ul class="htabs">
                <li><a href="#item-tab">Category</a></li>
                <li><a href="#advanced-tab">Advanced</a></li>
            </ul>
            <div id="item-tab">
                <div class="form">
                    <div>
                        <?php echo form_label('<span class="required">*</span> Title:', 'title'); ?>
                        <?php echo form_input(array('name'=>'title', 'id'=>'title', 'value'=>set_value('title', !empty($Category->title) ? $Category->title : ''))); ?>
                    </div>
                    <div>
                        <?php echo form_label('<span class="required">*</span> URL Title:', 'url'); ?>
                        <?php echo form_input(array('name'=>'url_title', 'id'=>'url_title', 'value'=>set_value('url_title', !empty($Category->url_title) ? $Category->url_title : ''))); ?>
                    </div>
                    <div>
                        <?php echo form_label('Target:', 'target'); ?>
                        <?php echo form_dropdown('target', array(''=>'Current Window', '_blank'=>'New Tab / Window (_blank)'), set_value('target', !empty($Category->target) ? $Category->target : ''), 'id="target"'); ?>
                    </div>
                    <div>
                        <?php echo form_label('<span class="required">*</span> Subcategories Visibility:<span class="help">Show/Hide children of this item.</span>', 'subcategories_visibility'); ?>
                        <?php echo form_dropdown('subcategories_visibility', array('show' => 'Always Show', 'current_trail' => 'Only Show if in Current Trail', 'hide' => 'Never Show'), set_value('subcategories_visibility', !empty($Category->subcategories_visibility) ? $Category->subcategories_visibility : ''), 'id="subcategories_visibility"'); ?>
                    </div>
                    <div>
                        <?php echo form_label('Hide:<span class="help">Don\'t show this category in the category gorup list.</span>', 'hide'); ?>
                        <?php echo form_checkbox(array('name'=>'hide', 'id'=>'hide', 'value'=>'1', 'checked'=>set_checkbox('hide', '1', (!empty($Category->hide) && $Category->hide) ? TRUE : FALSE))); ?>
                    </div>
                </div>
            </div>
            <div id="advanced-tab">
                <div class="form">
                    <div>
                        <?php echo form_label('Tag ID:', 'tag_id'); ?>
                        <?php echo form_input(array('name'=>'tag_id', 'id'=>'tag_id', 'class'=>'long', 'value'=>set_value('tag_id', !empty($Category->tag_id) ? $Category->tag_id : ''))); ?>
                    </div>
                    <div>
                        <?php echo form_label('Class:', 'class'); ?>
                        <?php echo form_input(array('name'=>'class', 'id'=>'class', 'class'=>'long', 'value'=>set_value('class', !empty($Category->class) ? $Category->class : ''))); ?>
                    </div>
                </div>
            </div>
        </div>
        <?php echo form_close(); ?>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {

        $( "#tabs" ).tabs();

        // Auto fill URL Title based on title
        <?php if ( ! $edit_mode): ?>
        $('#title').keyup( function(e) {
            $('#url_title').val($(this).val().toLowerCase().replace(/\s+/g, '-').replace(/[^a-z0-9\-_]/g, ''))
        });
        <?php endif; ?>

    });
</script>
