<?php 

namespace CmsCanvas\Content\Type\FieldType;

use Theme, Admin, StringView;
use CmsCanvas\Content\Type\FieldType;

class Ckeditor extends FieldType {

    /**
     * Returns a view of additional settings for the ckeditor field
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        return view('cmscanvas::fieldType.ckeditor.settings')
            ->with('fieldType', $this);
    }

    /**
     * Returns an array of validation rules for the setting fields
     *
     * @return array
     */
    public function getSettingsValidationRules()
    {
        return [
            'settings.height' => 'integer|min:0' 
        ];
    }

    /**
     * Returns a view of the ckeditor field input
     *
     * @return \Illuminate\View\View
     */
    public function inputField()
    {
        Theme::addPackage('ckeditor');

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
                extraPlugins : 'stylesheetparser',
                contentsCss : ['" .Theme::asset('js/ckeditor/contents.css')."'],
                stylesSet : [],
                height : '300px',
                filebrowserBrowseUrl : '".Theme::asset('js/kcfinder/browse.php?type=files') . "',
                filebrowserImageBrowseUrl : '".Theme::asset('js/kcfinder/browse.php?type=images') . "',
                filebrowserFlashBrowseUrl : '".Theme::asset('js/kcfinder/browse.php?type=flash') . "',
                filebrowserUploadUrl : '".Theme::asset('js/kcfinder/upload.php?type=files') . "',
                filebrowserImageUploadUrl : '".Theme::asset('js/kcfinder/upload.php?type=images') . "',
                filebrowserFlashUploadUrl : '".Theme::asset('js/kcfinder/upload.php?type=flash') . "'
            };

            $('textarea.ckeditor_textarea').each(function(index) {
                ckeditor_config.height = $(this).height();
                CKEDITOR.replace($(this).attr('name'), ckeditor_config); 
            });

        });";

        Theme::addInlineScript($config, true);

        return view('cmscanvas::fieldType.ckeditor.input')
            ->with('fieldType', $this);
    }

    /**
     * Returns editable content
     *
     * @return string
     */
    public function renderEditableContents()
    {
        // Set up the session for KCFinder
        if (session_id() == '') {
            @session_start();
        }

        $_SESSION['KCFINDER'] = [];
        $_SESSION['KCFINDER']['disabled'] = false;
        $_SESSION['isLoggedIn'] = true;

        Theme::addJavascript(Theme::asset('js/content_fields/ckeditor_inline_editable.js', 'admin'));
        return '<div id="'.$this->getInlineEditableKey().'" class="cc_admin_editable cc_ck_editable" contenteditable="true">'
            .$this->renderContents()
            .'</div>';
    }

    /**
     * Returns the data for the current field type
     *
     * @return string
     */
    public function getData()
    {
        // Convert appllicable HTML characters to entities so that we can have
        // code snippets in the editor without it being converted.
        return htmlentities($this->data);
    }

}