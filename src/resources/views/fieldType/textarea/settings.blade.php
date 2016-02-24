<div>
    <label>Rows: <span class="help">Default: 5</span></label>
    {!! 
        Form::text(
            $fieldType->getSettingsKey('rows'),
            $fieldType->getSetting('rows'),
            ['style' => 'width: 50px;']
        )
    !!}
</div>