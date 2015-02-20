<div>
    {!! HTML::decode(Form::label('field_id_'.$fieldType->field->id, '<div class="arrow arrow_expand"></div>'.(($fieldType->field->required) ? '<span class="required">*</span> ' : ''). $fieldType->field->label)) !!}
    <div>
    	@if ($fieldType->field->translate)
			<div class="tabs">
			    <ul class="htabs">
					@foreach ($languages as $language)
				        <li><a href="#translate-field_id_{!! $fieldType->field->id !!}_{!! $language->locale !!}">{!! $language->language !!}</a></li>
					@endforeach
			    </ul>
		    	@foreach ($languages as $language)
			    	<div id="translate-field_id_{!! $fieldType->field->id !!}_{!! $language->locale !!}">
			    		<?php
					    	$dataItem = $fieldDataItems->getFirstWhere('locale', $language->locale);
					    	$data = ($dataItem != null) ? $dataItem->data : '';
                            $metadata = ($dataItem != null) ? $dataItem->metadata : '';

				    		$fieldType->setLocale($language->locale);
				    		$fieldType->setData($data);
                            $fieldType->setMetadata($metadata);
			    		?>
				        {!! $fieldType->inputField() !!}
			    	</div>
		    	@endforeach
		    </div>
    	@else
	        {!! $fieldType->inputField() !!}
        @endif
    </div>
</div>