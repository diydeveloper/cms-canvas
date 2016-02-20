var jq_admin_toolbar = jQuery.noConflict(true);
var cc_editable_page_dirty = false;

// Turn off automatic editor creation first.
CKEDITOR.disableAutoInline = true;

jq_admin_toolbar(document).ready( function() {  
    jq_admin_toolbar.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN
        }
    });

    // Prepend the toolbar to the top of the page
    jq_admin_toolbar('#admin-toolbar').prependTo(document.body).show(); 

    // Enable superfish dropdown effects
    jq_admin_toolbar('#admin-toolbar > ul').superfish({
        hoverClass   : 'sfHover',
        pathClass    : 'overideThisToUse',
        delay        : 0,
        animation    : {height: 'show'},
        speed        : 'normal',
        autoArrows   : false,
        dropShadows  : false, 
        disableHI    : false, /* set to true to disable hoverIntent detection */
        onInit       : function(){},
        onBeforeShow : function(){},
        onShow       : function(){},
        onHide       : function(){}
    });

    // Toggle inline editing click event
    jq_admin_toolbar('#admin-toggle-inline-editing').click(function() {
        form = jq_admin_toolbar('<form>').attr({
            method: 'post'
        });

        jq_admin_toolbar('<input>').attr({
            type: 'hidden',
            name: 'admin_toggle_inline_editing',
            value: '1'
        }).appendTo(form);

        jq_admin_toolbar('<input>').attr({
            type: 'hidden',
            name: '_token',
            value: CSRF_TOKEN
        }).appendTo(form);

        form.submit();

        return false;
    });

    // Setup CKEditor for inline editing.
    var cc_text_config = { 
        toolbar : [
            { name: 'clipboard', items : [ 'Cut','Copy','Paste','-','Undo','Redo' ] },
        ],
        enterMode : CKEDITOR.ENTER_BR,
        shiftEnterMode : CKEDITOR.ENTER_BR,
        forcePasteAsPlainText : true,
        entities : true,
        floatSpacePinnedOffsetY : 40,
    };

    jq_admin_toolbar('.cc_admin_editable.cc_text_editable').each(function(index) {
        CKEDITOR.inline(jq_admin_toolbar(this).attr('id'), cc_text_config); 
    });

    jq_admin_toolbar('#admin-save-changes').click(function() {
        jq_admin_toolbar('#admin-save-status').html('Saving...').show();
        data = new Object();

        jq_admin_toolbar('.cc_admin_editable').each(function() {
            id = jq_admin_toolbar(this).attr('id');

            if (typeof CKEDITOR.instances[id] !== 'undefined') {
                data[id] = CKEDITOR.instances[id].getData();
            } else {
                data[id] = jq_admin_toolbar(this).html();
            }
        });

        jq_admin_toolbar('.cc_hidden_editable').each(function() {
            id = jq_admin_toolbar(this).attr('id');
            data[id] = jq_admin_toolbar(this).val();
        });

        jq_admin_toolbar.ajax({
            url: ADMIN_URL + "/content/entries/save-inline-content", 
            data: data,
            type: 'post',
            dataType: 'json',
            error: function(XMLHttpRequest, textStatus, errorThrown){
                jq_admin_toolbar('#admin-save-status').html('Error').show();
                alert('Error: ' + XMLHttpRequest.status + ' ' + XMLHttpRequest.statusText);
            },
            success: function(response){
                if (response.status == 'success') {
                    cc_editable_page_dirty = false;
                    jq_admin_toolbar('#admin-save-status').html('Saved').show().delay(5000).fadeOut();
                } else {
                    jq_admin_toolbar('#admin-save-status').html('Error').show();
                    alert('Error: \n' + response.message);
                }
            }
        });
        
        return false;
    });

    jq_admin_toolbar('.cc_admin_editable').live('focus', function() {
        id = jq_admin_toolbar(this).attr('id');

        if (typeof CKEDITOR.instances[id] !== 'undefined') {
            before = CKEDITOR.instances[id].getData();
        } else {
            before = jq_admin_toolbar(this).html();
        }
    }).live('blur keyup paste', function() { 
        if (!cc_editable_page_dirty) {
            id = jq_admin_toolbar(this).attr('id');

            if (typeof CKEDITOR.instances[id] !== 'undefined') {
                new_content = CKEDITOR.instances[id].getData();
            } else {
                new_content = jq_admin_toolbar(this).html();
            }

            if (typeof before !== 'undefined' && before != new_content) { 
                jq_admin_toolbar(this).trigger('change'); 
            }
        }
    });

    jq_admin_toolbar('.cc_admin_editable').live('change', function() {
        cc_editable_page_dirty = true;
        jq_admin_toolbar('#admin-save-status').html('Unsaved').show();
    }); 

    function admin_unload_page() { 
        if(cc_editable_page_dirty){
            return "You have unsaved changes on this page. Do you want to leave this page and discard your changes or stay on this page?";
        }
    }

    window.onbeforeunload = admin_unload_page;
});