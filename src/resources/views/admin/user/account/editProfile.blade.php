@extends('cmscanvas::admin.user.account.master')

@section('accountView')
    <h2>Edit Profile</h2>
    {!! Form::model($user, ['id' => 'form']) !!}
    <div class="form">
        <div>
            {!! HTML::decode(Form::label('email', '<span class="required">*</span> Email:')) !!}
            {!! Form::text('email') !!}
        </div>

        <div>
            {!! HTML::decode(Form::label('first_name', '<span class="required">*</span> First Name:')) !!}
            {!! Form::text('first_name') !!}
        </div>

        <div>
            {!! HTML::decode(Form::label('last_name', '<span class="required">*</span> Last Name:')) !!}
            {!! Form::text('last_name') !!}
        </div>

        <div>
            {!! HTML::decode(Form::label('timezone_id', '<span class="required">*</span> Timezone:')) !!}
            {!! Form::select('timezone_id', ['' => ''] + $timezones->lists('name', 'id')->all()) !!}
        </div>

        <div>
            {!! Form::label('phone', 'Phone:') !!}
            {!! Form::text('phone') !!}
        </div>

        <div>
            {!! Form::label('address', 'Address:') !!}
            {!! Form::text('address') !!}
        </div>

        <div>
            {!! Form::label('address2', 'Address 2:') !!}
            {!! Form::text('address2') !!}
        </div>

        <div>
            {!! Form::label('city', 'City:') !!}
            {!! Form::text('city') !!}
        </div>

        <div>
            {!! Form::label('state', 'State / Province:') !!}
            {!! Form::text('state') !!}
        </div>

        <div>
            {!! Form::label('country', 'Country:') !!}
            {!! Form::text('country') !!}
        </div>

        <div class="no_border">
            {!! Form::label('zip', 'Zip:') !!}
            {!! Form::text('zip') !!}
        </div>

    </div>
    <br />
    <div class="buttons">
        <a class="button" href="javascript:void(0);" id="save" onClick="$('#form').submit()"><span>Save</span></a>
    </div>
    {!! Form::close() !!}
@stop