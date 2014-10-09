<div>
    <label>Height: <span class="help">Default: 300px</span></label>
    {{ 
        Form::text(
            $fieldType->getSettingsKey('height'),
            $fieldType->getSetting('height'),
            array('style' => 'width: 50px;')
        )
    }} px
</div>

<div>
    <label>Allow Inline Editing:</label>
    <span>
        {{ Form::radio($fieldType->getSettingsKey('inline_editing'), 1, (bool) $fieldType->getSetting('inline_editing', true)) }}
        <label for="inline_editing_yes">Yes</label>
        {{ Form::radio($fieldType->getSettingsKey('inline_editing'), 0, ! (bool) $fieldType->getSetting('inline_editing', true)) }}
        <label for="inline_editing_no">No</label>
    </span>
</div>