<div>
    <label for="type">Options: <span class="help">Put each item on a seperate line. <br /><br />Syntax:<br />label or value=label</span></label>
    {!! Form::textarea('options', (isset($fieldType->getField()->options)) ? $fieldType->getField()->options : null) !!}
</div>
