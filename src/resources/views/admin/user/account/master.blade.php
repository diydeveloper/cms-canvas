<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/user.png') !!}"> My Account</h1>
    </div>
    <div class="content">
        <div class="fleft" style="background-color: #f4f4f4;">
            <ul class="side_nav">
                <li><a {!! (Request::is('*/user/account')) ? 'class="selected"' : '' !!} href="{!! Admin::url('user/account') !!}">Edit Profile &amp; Settings</a></li>
                <li><a {!! (Request::is('*/user/account/update-avatar')) ? 'class="selected"' : '' !!} href="{!! Admin::url('user/account/update-avatar') !!}">Update Avatar</a></li>
                <li class="no_border"><a {!! (Request::is('*/user/account/change-password')) ? 'class="selected"' : '' !!} href="{!! Admin::url('user/account/change-password') !!}">Change Password</a></li>
            </ul>
        </div>
        <div style="margin-left: 220px;">
            @yield('accountView')
        </div>
        <div class="clear"></div>
    </div>
</div>