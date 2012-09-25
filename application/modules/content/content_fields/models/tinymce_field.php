<?php (defined('BASEPATH')) OR exit('No direct script access allowed');

class Tinymce_field extends Field_type
{
    function view($data)
    {
        $this->template->add_package(array('tinymce'));

        $config = "$(document).ready( function() {
            tinyMCE.init({
                // General options
                mode : \"specific_textareas\",
                editor_selector : \"tinymce\",

                plugins : \"pagebreak,style,advhr,advimage,advlink,inlinepopups,preview,media,contextmenu,paste,fullscreen,noneditable,visualchars,xhtmlxtras,template,advlist,table,layer,codeprotect\",

                convert_urls : false,
                height : \"300\",

                // Theme options
                theme : \"advanced\",
                skin : \"o2k7\",
                skin_variant : \"silver\",
                theme_advanced_buttons1 : \"bold,italic,underline,strikethrough,blockquote,|,bullist,numlist,|,justifyleft,justifycenter,justifyright,justifyfull,|,styleselect,formatselect,fontselect,fontsizeselect\",
                theme_advanced_buttons2 : \"cut,copy,paste,pastetext,pasteword,|,outdent,indent,|,sub,sup,|,link,unlink,anchor,|,image,insertimage,media,|,forecolor,backcolor,|,charmap,emotions,advhr,|,undo,redo,|,hr,removeformat,visualaid,|,preview,fullscreen,code\",
                theme_advanced_buttons3 : \"tablecontrols,|,insertlayer,moveforward,movebackward,absolute\",

                theme_advanced_toolbar_location : \"top\",
                theme_advanced_toolbar_align : \"left\",
                theme_advanced_resizing : true,
                theme_advanced_statusbar_location : \"bottom\",
                theme_advanced_resize_horizontal : false,
                theme_advanced_blockformats : \"p,h1,h2,h3,h4,h5,h6,div,pre\",

                file_browser_callback: 'openKCFinder',

                // Drop lists for link/image/media/template dialogs
                external_link_list_url : \"" . site_url(ADMIN_PATH . "/content/entries/links") . "\",
                " . 
                (($this->settings->editor_stylesheet) ? "content_css : \"" . site_url(ADMIN_PATH . '/content/entries/css/' . $data['Entry']->id) . "?\" + new Date().getTime()" : "")
            . "}); 

        });

        function openKCFinder(field_name, url, type, win) {
            tinyMCE.activeEditor.windowManager.open({
                file: '" . theme_url('/assets/js/kcfinder/browse.php') . "?opener=tinymce&type=' + type + (type == 'image' ? 's' : ''),
                title: 'KCFinder',
                width: 700,
                height: 500,
                resizable: \"yes\",
                inline: true,
                close_previous: \"no\",
                popup_css: false
            }, {
                window: win,
                input: field_name
            });
            return false;
        }";

        if ( ! in_array($config, $this->template->scripts))
        {
            $this->template->add_script($config);
        }

        return $this->load->view('tinymce', $data, TRUE);
    }
}
