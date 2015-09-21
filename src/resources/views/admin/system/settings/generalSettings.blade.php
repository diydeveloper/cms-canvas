<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/setting.png') !!}"> General Settings</h1>

        <div class="buttons">
            <a class="button" href="#" onClick="$('#settings_form').submit();"><span>Save</span></a>
        </div>
    </div>
    <div class="content">

        {!! Form::model($settings, array('id' => 'settings_form')) !!}
        <div class="tabs">
            <ul class="htabs">
                <li><a href="#general-tab">General</a></li>
                <li><a href="#analytics-tab">Analytics</a></li>
            </ul>
            <!-- General Tab -->
            <div id="general-tab">
                <div class="form">
                    <div>
                        {!! HTML::decode(Form::label('email', '<span class="required">*</span> Site Name:')) !!}
                        {!! Form::text('site_name') !!}
                    </div>

                    <div>
                        {!! HTML::decode(Form::label('notification_email', '<span class="required">*</span> Notification Email:')) !!}
                        {!! Form::text('notification_email') !!}
                    </div>

                    <div>
                        {!! HTML::decode(Form::label('site_homepage', '<span class="required">*</span> Site Homepage:')) !!}
                        {!! Form::select('site_homepage', $entries->lists('title', 'id')->all()) !!}
                    </div>

                    <div>
                        {!! HTML::decode(Form::label('custom_404', '<span class="required">*</span> Custom 404:')) !!}
                        {!! Form::select('custom_404', $entries->lists('title', 'id')->all()) !!}
                    </div>

                    <div>
                        {!! HTML::decode(Form::label('theme', '<span class="required">*</span> Theme:')) !!}
                        {!! Form::select('theme', $themes) !!}
                    </div>

                    <div>
                        {!! HTML::decode(Form::label('layout', '<span class="required">*</span> Default Layout:')) !!}
                        {!! Form::select('layout', $layouts) !!}
                        <span id="layout_ex" class="ex"></span>
                    </div>
                </div>
            </div>

            <!-- Analytics Tab -->
            <div id="analytics-tab">
                <div class="form">
                    <div>
                        {!! Form::label('ga_tracking_id', 'GA Tracking Code:') !!}
                        {!! Form::text('ga_tracking_id') !!}
                    </div>
                </div>
            </div>
        </div>
        {!! Form::close() !!}

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $( ".tabs" ).tabs();

        $('#theme').change( function() {

            $('#layout').html('');
            $('#layout_ex').html('Loading Layouts...');

            $.post('{!! Admin::url('/system/theme-layouts') !!}', {theme: $('#theme').val()}, function(response) {
                if (response.status == 'OK')
                {
                    $.each(response.layouts, function(i , val) {
                        $('#layout').append('<option value="' + val + '">' + val + '</option>');
                    });
                    $('#layout_ex').html('');
                }
                else
                {
                    $('#layout_ex').html(response.message);
                }
            }, 'json');

        });
    });
</script>