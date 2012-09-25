<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/category.png'); ?>"> Navigation Item Edit</h1>

        <div class="buttons">
            <a class="button" href="#" onClick="$('#navigation_item_edit').submit()"><span>Save</span></a>
        </div>
    </div>
    <div class="content">

        <?php echo form_open(null, 'id="navigation_item_edit"') ?>
        <div id="tabs">
            <ul class="htabs">
                <li><a href="#item-tab">Nav Item</a></li>
                <li><a href="#advanced-tab">Advanced</a></li>
            </ul>
            <div id="item-tab">
                <div class="form">
                    <div>
                        <?php echo form_label('<span class="required">*</span> Link Type:', 'type')?>
                        <?php echo form_dropdown('type', array('page'=>'Page', 'url'=>'URL', 'dynamic_route'=>'Dynamic Route'), set_value('type', !empty($Navigation_item->type) ? $Navigation_item->type : ''), 'id="link_type"')?>
                    </div>
                    <div class="page_div">
                        <?php echo form_label('<span class="required">*</span> Pages:', 'entry_id')?>
                        <?php echo form_dropdown('entry_id', $Pages, set_value('entry_id', !empty($Navigation_item->entry_id) ? $Navigation_item->entry_id : ''), 'id="entry_id"')?>
                    </div>
                    <div class="page_div">
                        <?php echo form_label('Link Text:<span class="help">Leave blank to use the page title</span>', 'title')?>
                        <?php echo form_input(array('name'=>'page_link_text', 'id'=>'page_link_text', 'value'=>set_value('page_link_text', !empty($Navigation_item->title) ? $Navigation_item->title : '')))?>
                    </div>
                    <div class="url_div">
                        <?php echo form_label('<span class="required">*</span> Link Text:', 'title')?>
                        <?php echo form_input(array('name'=>'title', 'id'=>'title', 'class'=>'long', 'value'=>set_value('title', !empty($Navigation_item->title) ? $Navigation_item->title : '')))?>
                    </div>
                    <div class="url_div">
                        <?php echo form_label('<span class="required">*</span> URL:', 'url')?>
                        <?php echo form_input(array('name'=>'url', 'id'=>'url', 'class'=>'long','value'=>set_value('url', !empty($Navigation_item->url) ? $Navigation_item->url : '')))?>
                    </div>
                    <div>
                        <?php echo form_label('Target:', 'target')?>
                        <?php echo form_dropdown('target', array(''=>'Current Window', '_blank'=>'New Tab / Window (_blank)'), set_value('target', !empty($Navigation_item->target) ? $Navigation_item->target : ''), 'id="target"')?>
                    </div>
                    <div>
                        <?php echo form_label('<span class="required">*</span> Subnav Visibility:<span class="help">Show/Hide children of this item.</span>', 'subnav_visibility')?>
                        <?php echo form_dropdown('subnav_visibility', array('1' => 'Always Show', '2' => 'Only Show if in Current Trail', '3' => 'Never Show'), set_value('subnav_visibility', !empty($Navigation_item->subnav_visibility) ? $Navigation_item->subnav_visibility : ''), 'id="subnav_visibility"')?>
                    </div>
                    <div>
                        <?php echo form_label('Hide:<span class="help">Don\'t show this item in the navigation.</span>', 'hide')?>
                        <?php echo form_checkbox(array('name'=>'hide', 'id'=>'hide', 'value'=>'1', 'checked'=>set_checkbox('hide', '1', (!empty($Navigation_item->hide) && $Navigation_item->hide) ? TRUE : FALSE)))?>
                    </div>
                </div>
            </div>
            <div id="advanced-tab">
                <div class="form">
                    <div>
                        <?php echo form_label('Tag ID:', 'tag_id')?>
                        <?php echo form_input(array('name'=>'tag_id', 'id'=>'tag_id', 'class'=>'long', 'value'=>set_value('tag_id', !empty($Navigation_item->tag_id) ? $Navigation_item->tag_id : '')))?>
                    </div>
                    <div>
                        <?php echo form_label('Class:', 'class')?>
                        <?php echo form_input(array('name'=>'class', 'id'=>'class', 'class'=>'long', 'value'=>set_value('class', !empty($Navigation_item->class) ? $Navigation_item->class : '')))?>
                    </div>
                     <div>
                         <?php echo form_label('Disable Current:<span class="help">Don\'t allow this item to be marked as current.</span>', 'disable_current')?>
                         <?php echo form_checkbox(array('name'=>'disable_current', 'id'=>'disable_current', 'value'=>'1', 'checked'=>set_checkbox('disable_current', '1', (!empty($Navigation_item->disable_current) && $Navigation_item->disable_current) ? TRUE : FALSE)))?>
                     </div>
                     <div>
                         <?php echo form_label('Disable Current Trail:<span class="help">Don\'t allow this item to be marked with current trail.</span>', 'disable_current_trail')?>
                         <?php echo form_checkbox(array('name'=>'disable_current_trail', 'id'=>'disable_current_trail', 'value'=>'1', 'checked'=>set_checkbox('disable_current_trail', '1', (!empty($Navigation_item->disable_current_trail) && $Navigation_item->dissable_current_trail) ? TRUE : FALSE)))?>
                     </div>
                </div>
            </div>
        </div>
        <?php echo form_close() ?>
    </div>
</div>

<script type="text/javascript">
$(document).ready(function() {

    $('#link_type').change(function() {
        if ($(this).val() == 'page')
        {
            $('.url_div').hide();    
            $('.page_div').show();    
        }
        else
        {
            $('.page_div').hide();    
            $('.url_div').show();    
        }
    });

    $('#link_type').trigger('change');

    $( "#tabs" ).tabs();

});
</script>
