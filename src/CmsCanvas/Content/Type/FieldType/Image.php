<?php 

namespace CmsCanvas\Content\Type\FieldType;

use Theme, Content;
use CmsCanvas\Content\Type\FieldType;

class Image extends FieldType {

    /**
     * Returns a view of additional settings for the image field
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        return view('cmscanvas::fieldType.image.settings')
            ->with('fieldType', $this);
    }

    /**
     * Returns a view of the image field input
     *
     * @return \Illuminate\View\View
     */
    public function inputField()
    {
        Theme::addPackage('image_field');

        return view('cmscanvas::fieldType.image.input')
            ->with(['fieldType' => $this]);
    }

    /**
     * Returns the rendered data for the field
     *
     * @return mixed
     */
    public function renderContents()
    {
        $output = $this->getSetting('output_type');
        $tagId = $this->getSetting('id');
        $class = $this->getSetting('class');
        $maxWidth = $this->getSetting('max_width');
        $maxHeight = $this->getSetting('max_height');
        $crop = $this->getSetting('crop', false);
        $alt = $this->getMetadata('alt');

        if ($output == 'image') {
            if ($maxWidth !== null || $maxHeight !== null) {
                $source = Content::thumbnail(
                    $this->data, 
                    [
                        'width' => $maxWidth, 
                        'height' => $maxHeight, 
                        'crop' => $crop
                    ]
                );
            } else {
                $source = $this->data;
            }

            $image = '<img src="'.$source.'" ';

            if ($tagId != null) {
                $image .= 'id="'.$tagId.'" ';
            }

            if ($class != null) {
                $image .= 'class="'.$class.'" ';
            }

            if ($alt != null) {
                $image .= 'alt="'.$alt.'" ';
            }

            $image .= '/>';
        } else {
            $image = $this->data;
        }

        return $image;
    }

    /**
     * Returns editable content
     *
     * @return string
     */
    public function renderEditableContents()
    {
        if ($this->getSetting('output_type') != 'image') {
            return $this->renderContents();
        }
        
        $this->setSettingValue(
            'class', 
            trim($this->getSetting('class').' cc_image_editable')
        );

        // Set up the session for KCFinder
        if (session_id() == '') {
            @session_start();
        }

        $_SESSION['KCFINDER'] = [];
        $_SESSION['KCFINDER']['disabled'] = false;
        $_SESSION['isLoggedIn'] = true;

        Theme::addJavascript(Theme::asset('js/content_fields/image_inline_editable.js', 'admin'));
        return $this->renderContents()
            .'<input id="'.$this->getInlineEditableKey().'" class="cc_hidden_editable" type="hidden" value="'.$this->data.'" />';
    }

}