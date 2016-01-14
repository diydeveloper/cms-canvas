<div>
    <label>Height: <span class="help">Default: 300px</span></label>
    {!! 
        Form::text(
            $fieldType->getSettingsKey('height'),
            $fieldType->getSetting('height'),
            array('style' => 'width: 50px;')
        )
    !!} px
</div>