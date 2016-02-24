<div>
    {!!
        Form::textarea(
            $fieldType->getKey(),
            $fieldType->getData(),
            [
                'rows' => $fieldType->getSetting('rows', '5'), 
            ]
        )
    !!}
</div>
