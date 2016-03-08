<div class="box">
    <div class="heading">
        @if ( ! empty($language))
            <h1><img alt="" src="{!! Theme::asset('images/language.png') !!}">Edit Language - {!! $language->language !!}</h1>
        @else
            <h1><img alt="" src="{!! Theme::asset('images/language.png') !!}">Add Language</h1>
        @endif

        <div class="buttons">
            <a class="button" href="javascript:void(0);" id="save" onClick="$('#form').submit();"><span>Save</span></a>
            <a class="button" href="{!! Admin::url('system/language') !!}"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">

        @if ( ! empty($language))
            {!! Form::model($language, array('id' => 'form')) !!}
        @else
            {!! Form::open(array('id' => 'form')) !!}
        @endif
        <div>
            <div class="form">
                <div>
                    {!! HTML::decode(Form::label('language', '<span class="required">*</span> Language:')) !!}
                    {!! Form::text('language') !!}
                </div>

                <div>
                    {!! HTML::decode(Form::label('locale', '<span class="required">*</span> Locale:')) !!}
                    {!! Form::text('locale') !!}
                </div>

                <div>
                    {!! HTML::decode(Form::label('active', '<span class="required">*</span> Status:')) !!}
                    <span>
                        <label>{!! Form::radio('active', '1', true) !!} Active</label>
                        <label>{!! Form::radio('active', '0') !!} Inactive</label>
                    </span>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>