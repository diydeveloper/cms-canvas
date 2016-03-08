@extends('cmscanvas::admin.user.account.master')

@section('accountView')
    <h2>Change Password</h2>
    {!! Form::open(['id' => 'form', 'files' => true]) !!}
    <div class="form">
        <div>
            {!! HTML::decode(Form::label('password', 'Password:')) !!}
            {!! Form::password('password') !!}
        </div>

        <div>
            {!! HTML::decode(Form::label('password_confirmation', 'Confirm Password:')) !!}
            {!! Form::password('password_confirmation') !!}
        </div>
    </div>
    <br />
    <div class="buttons">
        <a class="button" href="javascript:void(0);" id="save" onClick="$('#form').submit()"><span>Save</span></a>
    </div>
    {!! Form::close() !!}
@stop