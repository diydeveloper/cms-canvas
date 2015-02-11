<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/user.png') !!}"> {!! ( ! empty($user)) ? 'Edit' : 'Add' !!} User</h1>

        <div class="buttons">
            <a class="button" href="javascript:void(0);" id="save" onClick="$('#user_edit_form').submit()"><span>Save</span></a>
            <a class="button" href="{!! Admin::url('user') !!}"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">
        @if ( ! empty($user))
            {!! Form::model($user, array('id' => 'user_edit_form')) !!}
        @else
            {!! Form::open(array('id' => 'user_edit_form')) !!}
        @endif

        @if ( ! empty($user))
            <div class="tabs">
                <ul class="htabs">
                    <li><a href="#edit-user-tab">Edit User</a></li>
                    <li><a href="#password-tab">Password</a></li>
                </ul>
        @endif

            <div id="edit-user-tab">
                <div class="form">
                    <div>
                        {!! HTML::decode(Form::label('email', '<span class="required">*</span> Email:')) !!}
                        {!! Form::text('email') !!}
                    </div>

                    @if (empty($user))
                        <div>
                            {!! HTML::decode(Form::label('password', '<span class="required">*</span> Password:')) !!}
                            {!! Form::password('password') !!}
                        </div>

                        <div>
                            {!! HTML::decode(Form::label('password_confirmation', '<span class="required">*</span> Confirm Password:')) !!}
                            {!! Form::password('password_confirmation') !!}
                        </div>
                    @endif

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
                        {!! Form::select('timezone_id', ['' => ''] + $timezones->lists('name', 'id')) !!}
                    </div>

                    <div>
                        {!! Form::label('roles', 'Roles:') !!}
                        <div class="fields_wrapper">
                            <div class="scrollbox">
                            <?php $i = 0; ?>
                            @foreach ($roles as $role)
                                <div class="{!! ($i % 2 == 0) ? 'even' : 'odd' !!}">
                                    <label>
                                    {!! 
                                        Form::checkbox(
                                            'user_roles[]', 
                                            $role->id, 
                                            (($user != null && $user->hasRole($role->name)) ? true : false)
                                        ) 
                                    !!} 
                                    {!! $role->name !!}
                                    </label>
                                </div>
                                <?php $i++; ?>
                            @endforeach
                            </div>
                        </div>
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
                        {!! Form::label('state', 'State:') !!}
                        {!! Form::text('state') !!}
                    </div>

                    <div>
                        {!! Form::label('country', 'Country:') !!}
                        {!! Form::text('country') !!}
                    </div>

                    <div>
                        {!! Form::label('zip', 'Zip:') !!}
                        {!! Form::text('zip') !!}
                    </div>

                    <div>
                        {!! HTML::decode(Form::label('active', 'Status: <span class="help">Allow user to log in.</span>')) !!}
                        <span>
                            {!! Form::radio('active', '1', true) !!}
                            <label for="status_disabled">Enabled</label>
                            {!! Form::radio('active', '0') !!}
                            <label for="status_disabled">Disabled</label>
                        </span>
                    </div>
                </div>
            </div>

            @if ( ! empty($user))
                    <div id="password-tab">
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
                    </div>
                </div>
            @endif

            <div class="clear"></div>

            {!! Form::close() !!}
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $( ".tabs" ).tabs();
    });
</script>
