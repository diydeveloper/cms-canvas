<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Ckeditor_field extends Field_type
{
    function settings()
    {
        $data = get_object_vars($this);
        
        return $this->load->view('settings/ckeditor', $data, TRUE);
    }

    function display_field()
    {
        $data = get_object_vars($this);

        $this->template->add_package(array('ckeditor'));

        $config = "$(document).ready( function() {
            var ckeditor_config = { 
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
                extraPlugins : 'stylesheetparser'," .
                (($this->settings->editor_stylesheet) ? "contentsCss : ['" . site_url(ADMIN_PATH . '/content/entries/css/' . $this->Entry->id) . "?' + new Date().getTime()  ]," : "")
                 . "stylesSet : [],
                height : '300px',
                filebrowserBrowseUrl : '" . theme_url('/assets/js/kcfinder/browse.php?type=files') . "',
                filebrowserImageBrowseUrl : '" . theme_url('/assets/js/kcfinder/browse.php?type=images') . "',
                filebrowserFlashBrowseUrl : '" . theme_url('/assets/js/kcfinder/browse.php?type=flash') . "',
                filebrowserUploadUrl : '" . theme_url('/assets/js/kcfinder/upload.php?type=files') . "',
                filebrowserImageUploadUrl : '" . theme_url('/assets/js/kcfinder/upload.php?type=images') . "',
                filebrowserFlashUploadUrl : '" . theme_url('/assets/js/kcfinder/upload.php?type=flash') . "'
            };

            $('textarea.ckeditor_textarea').each(function(index) {
                ckeditor_config.height = $(this).height();
                CKEDITOR.replace($(this).attr('name'), ckeditor_config); 
            });

        });";

        if ( ! in_array($config, $this->template->scripts))
        {
            $this->template->add_script($config);
        }

        return $this->load->view('ckeditor', $data, TRUE);
    }

    function output()
    {
        if (is_inline_editable($this->Entry->content_type_id))
        {
            $this->_inline_editing_config();
            return '<div id="cc_field_' . $this->Entry->id . '_'. $this->Field->id  . '" class="cc_admin_editable cc_ck_editable" contenteditable="true">{{ noparse }}' . $this->content . '{{ /noparse }}</div>';
        }
        else
        {
            return $this->content;
        }
    }

    private function _inline_editing_config()
    {
        $this->template->add_package('ckeditor');
        $config = "jq_admin_toolbar(document).ready( function() { 
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
        });";

        if ( ! in_array($config, $this->template->scripts))
        {
            $this->template->add_script($config);
        }
    }
}
