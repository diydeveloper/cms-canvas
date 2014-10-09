<?php namespace CmsCanvas\Content\Type\FieldType;

use View, Input, Theme, Content;
use CmsCanvas\Content\Type\FieldType;

class Image extends FieldType {

    /**
     * Returns a view of additional settings for the image field
     *
     * @return \Illuminate\View\View
     */
    public function settings()
    {
        return View::make('CmsCanvas\Content\Type\FieldType::image.settings')
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

        return View::make('CmsCanvas\Content\Type\FieldType::image.input')
            ->with(array('fieldType' => $this));
    }

    /**
     * Returns the rendered data for the field
     *
     * @return mixed
     */
    public function render()
    {
        $output = $this->getSetting('output_type');
        $tagId = $this->getSetting('id');
        $class = $this->getSetting('class');
        $maxWidth = $this->getSetting('max_width');
        $maxHeight = $this->getSetting('max_height');
        $crop = $this->getSetting('crop', false);
        $inlineEditing = $this->getSetting('inline_editing', false);

        if ($output == 'image')
        {
            if ($maxWidth !== null || $maxHeight !== null)
            {
                $source = Content::thumbnail($this->data, $maxWidth, $maxHeight, $crop);
            }
            else
            {
                $source = $this->data;
            }

            $image = '<img src="'.$source.'" ';

            if ($tagId != null) 
            {
                $image .= 'id="'.$tagId.'" ';
            }

            if ($class != null) 
            {
                $image .= 'class="'.$class.'" ';
            }

            $image .= '/>';
        }
        else
        {
            $image = $this->data;
        }

        return $image;
    }

}