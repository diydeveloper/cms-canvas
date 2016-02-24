<div>
    <span>
    @foreach ($optionArray as $value => $label)
        <div>
            <label>{!! Form::radio($fieldType->getKey(), $value, (($value == $fieldType->getData()) ? true : false)) !!} {!! $label !!}</label>
        </div>
    @endforeach
    </span>
</div>