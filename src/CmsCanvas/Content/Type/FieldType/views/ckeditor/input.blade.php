<div>
    {{
        Form::textarea(
            $fieldType->getKey(),
            $fieldType->data,
            array(
                'class' => 'textarea_content ckeditor_textarea', 
                'style' => ( ! empty($fieldType->settings->height)) ? 'height: ' . $fieldType->settings->height . 'px;' : 'height: 300px;', 
            )
        )
    }}
</div>
