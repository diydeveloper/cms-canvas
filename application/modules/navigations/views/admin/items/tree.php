<div class="box">
    <div class="heading">
        <h1><img alt="" src="<?php echo theme_url('assets/images/category.png'); ?>"> Navigation Tree - <?php echo $Navigation->title; ?> (#<?php echo $navigation_id; ?>)</h1>

        <div class="buttons">
            <a class="button" href="<?php echo site_url(ADMIN_PATH . "/navigations/items/edit/$navigation_id"); ?>" ><span>Add Item</span></a>
        </div>
    </div>

    <div id="tree_box" class="content" style="clear: both;">
        <?php if ( ! empty($Tree)): ?>
            <?php echo $Tree; ?>
        <?php else: ?>
            <br/>
            <br/>
            <div class="align_center">No navigation items have been added.</div>
            <br/>
            <br/>
        <?php endif; ?>
    </div>
</div>

<?php js_start(); ?>
<script type="text/javascript">
    $(document).ready(function(){
        $('ol.sortable').nestedSortable({
            disableNesting: 'no-nest',
            forcePlaceholderSize: true,
            handle: 'div span.sortable_handle',
            helper: 'clone',
            items: 'li',
            maxLevels: 4,
            opacity: .6,
            placeholder: 'placeholder',
            revert: 250,
            tabSize: 25,
            tolerance: 'pointer',
            toleranceElement: '> div',
            update: function(event, ui) { 
                show_status('Saving...', false, true);
                dataString = $('ol.sortable').nestedSortable('serialize');
                $.ajax({  
                    type: "POST",  
                    url: "<?php echo site_url(ADMIN_PATH . '/navigations/items/save-tree'); ?>",  
                    data: dataString,  
                    success: function(html) {  
                        show_status('Saved', true, false);
                    },
                    error:function (xhr, ajaxOptions, thrownError){
                        hide_status();
                        alert('Status: ' + xhr.responseText);
                    }   
                });   
            }
        });

        // Delete listner
        $('.delete').click(function() {
            return confirm('Are you sure you want to delete this item and any sub items it may have?');
        });
    });
</script>
<?php js_end(); ?>