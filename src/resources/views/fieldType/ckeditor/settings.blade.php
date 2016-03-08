<div>
    {!! Form::label($fieldType->getSettingsKey('inline_editable'), 'Inline Editable:') !!}
    <span>
        <label>{!! Form::radio($fieldType->getSettingsKey('inline_editable'), '1', (bool) $fieldType->getSetting('inline_editable', true)) !!} Yes</label>
        <label>{!! Form::radio($fieldType->getSettingsKey('inline_editable'), '0', (bool) ! $fieldType->getSetting('inline_editable', true)) !!} No</label>
    </span>
</div>

<div>
    <label>Height: <span class="help">Default: 300px</span></label>
    {!! 
        Form::text(
            $fieldType->getSettingsKey('height'),
            $fieldType->getSetting('height'),
            ['style' => 'width: 50px;']
        )
    !!} px
</div>