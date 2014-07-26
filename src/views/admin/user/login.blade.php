<div id="login_box" class="box">
    <div class="heading">
        <h1><img src="{{ Theme::asset('images/lockscreen.png') }}" alt="" /> Please enter your login details.</h1>
    </div>
    <div id="login_form" class="content">
        {{ Form::open() }}

            @include('theme::partials.notifications')

            <div class="image">
                <img src="{{ Theme::asset('images/login.png') }}" alt="Please enter your login details." />
                <br />
            </div>

            <div class="fields">
                <div>
                    {{ Form::label('email', 'Email') }}
                    {{ Form::text('email') }}
                </div>

                <div>
                    {{ Form::label('password', 'Password') }}
                    {{ Form::password('password') }}
                </div>

                <div>
                    <div class="fleft">
                        <label><input name="remember_me" class="remember_me" type="checkbox" value="1" /> Remember Me</label>
                    </div>
                    <div class="fright">
                        <button class="button" type="submit"><span>Login</span></button>
                    </div>
                    <div class="clear"></div>
                </div>
                

            </div>

        {{ Form::close() }}
    </div>
</div>
