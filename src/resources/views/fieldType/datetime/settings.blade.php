<div>
    {!! Form::label($fieldType->getSettingsKey('timezone'), 'Render Timezone:') !!}
    {!! Form::select(
        $fieldType->getSettingsKey('timezone'),
        [
            ''  => 'None', 
            'users_timezone' => 'Auth User\'s Timezone',
            'site_timezone' => 'Site Timezone'
        ],
        $fieldType->getSetting('timezone')
    ) !!}
</div>