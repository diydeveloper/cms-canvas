jq_admin_toolbar(document).ready( function() { 
    var cc_ckeditor_config = { 
        toolbar : [
            { name: 'styles', items : [ 'Styles','Format','Font','FontSize' ] },
            { name: 'colors', items : [ 'TextColor','BGColor' ] },
            { name: 'paragraph', items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv','- ','JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock' ] },
            { name: 'tools', items : [ 'ShowBlocks' ] },
            { name: 'tools', items : [ 'Maximize' ] },
                            '/',
            { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Subscript','Superscript','Strike','-','RemoveFormat' ] },
            { name: 'clipboard', items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
            { name: 'editing', items : [ 'Find','Replace','-','Scayt' ] },
            { name: 'insert', items : [ 'Image','Flash','MediaEmbed','Table','HorizontalRule','SpecialChar','Iframe' ] },
            { name: 'links', items : [ 'Link','Unlink','Anchor' ] },
            { name: 'document', items : [ 'Source' ] }
        ],
        entities : true,
        extraPlugins : 'stylesheetparser',
        stylesSet : [],
        floatSpacePinnedOffsetY : 40,
        filebrowserBrowseUrl : ADMIN_ASSETS + '/js/kcfinder/browse.php?type=files',
        filebrowserImageBrowseUrl : ADMIN_ASSETS + '/js/kcfinder/browse.php?type=images',
        filebrowserFlashBrowseUrl : ADMIN_ASSETS + '/js/kcfinder/browse.php?type=flash',
        filebrowserUploadUrl : ADMIN_ASSETS + '/js/kcfinder/upload.php?type=files',
        filebrowserImageUploadUrl : ADMIN_ASSETS + '/js/kcfinder/upload.php?type=images',
        filebrowserFlashUploadUrl : ADMIN_ASSETS + '/js/kcfinder/upload.php?type=flash'
    };

    jq_admin_toolbar('.cc_admin_editable.cc_ck_editable').each(function(index) {
        CKEDITOR.inline(jq_admin_toolbar(this).attr('id'), cc_ckeditor_config); 
    });
});