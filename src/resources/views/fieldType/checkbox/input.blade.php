<div>
    <span>
    @foreach ($optionArray as $value => $label)
        <div>
            <label>{!! Form::checkbox($fieldType->getKey().'[]', $value, ((in_array($value, $fieldType->getData())) ? true : false)) !!} {!! $label !!}</label>
        </div>
    @endforeach
    <?php 
        /**
         * Since browsers do not POST checkboxes if they are unchecked...
         * This hidden field is used to identify that the checkboxes were present when the form was submitted. 
        */
    ?>
    {!! Form::hidden($fieldType->getKey().'_checkbox', 1) !!}
    </span>
</div>