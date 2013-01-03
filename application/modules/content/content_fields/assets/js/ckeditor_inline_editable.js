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
        filebrowserBrowseUrl : BASE_HREF + 'application/themes/admin/assets/js/kcfinder/browse.php?type=files',
        filebrowserImageBrowseUrl : BASE_HREF + 'application/themes/admin/assets/js/kcfinder/browse.php?type=images',
        filebrowserFlashBrowseUrl : BASE_HREF + 'application/themes/admin/assets/js/kcfinder/browse.php?type=flash',
        filebrowserUploadUrl : BASE_HREF + 'application/themes/admin/assets/js/kcfinder/upload.php?type=files',
        filebrowserImageUploadUrl : BASE_HREF + 'application/themes/admin/assets/js/kcfinder/upload.php?type=images',
        filebrowserFlashUploadUrl : BASE_HREF + 'application/themes/admin/assets/js/kcfinder/upload.php?type=flash'
    };

    jq_admin_toolbar('.cc_admin_editable.cc_ck_editable').each(function(index) {
        CKEDITOR.inline($(this).attr('id'), cc_ckeditor_config); 
    });
});