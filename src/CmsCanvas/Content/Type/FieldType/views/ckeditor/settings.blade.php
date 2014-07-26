<div>
    <label>Height: <span class="help">Default: 300px</span></label>
    {{ 
        Form::text(
            'settings[height]',
            ( ! empty($fieldType->settings->height)) ? $fieldType->settings->height : null,
            array('style' => 'width: 50px;')
        )
    }} px
</div>

<div>
    <label>Allow Inline Editing:</label>
    <span>
        {{ Form::radio('settings[inline_editing]', 1, ( ! isset($fieldType->settings->inline_editing) || $fieldType->settings->inline_editing) ? true : false) }}
        <label for="inline_editing_yes">Yes</label>
        {{ Form::radio('settings[inline_editing]', 0, (isset($fieldType->settings->inline_editing) && ! $fieldType->settings->inline_editing) ? true : false) }}
        <label for="inline_editing_no">No</label>
    </span>
</div>