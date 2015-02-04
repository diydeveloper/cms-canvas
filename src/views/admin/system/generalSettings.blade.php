<div class="box">
    <div class="heading">
        <h1><img alt="" src="{{ Theme::asset('images/setting.png') }}"> General Settings</h1>

        <div class="buttons">
            <a class="button" href="#" onClick="$('#settings_form').submit();"><span>Save</span></a>
        </div>
    </div>
    <div class="content">

        {{ Form::model($settings, array('id' => 'settings_form')) }}
        <div class="tabs">
            <ul class="htabs">
                <li><a href="#general-tab">General</a></li>
                <li><a href="#analytics-tab">Analytics</a></li>
            </ul>
            <!-- General Tab -->
            <div id="general-tab">
                <div class="form">
                    <div>
                        {{ HTML::decode(Form::label('email', '<span class="required">*</span> Site Name:')) }}
                        {{ Form::text('site_name') }}
                    </div>

                    <div>
                        {{ HTML::decode(Form::label('notification_email', '<span class="required">*</span> Notification Email:')) }}
                        {{ Form::text('notification_email') }}
                    </div>

                    <div>
                        {{ HTML::decode(Form::label('site_homepage', '<span class="required">*</span> Site Homepage:')) }}
                        {{ Form::select('site_homepage', $entries->lists('title', 'id')) }}
                    </div>

                    <div>
                        {{ HTML::decode(Form::label('custom_404', '<span class="required">*</span> Custom 404:')) }}
                        {{ Form::select('custom_404', $entries->lists('title', 'id')) }}
                    </div>

                    <div>
                        {{ HTML::decode(Form::label('theme', '<span class="required">*</span> Theme:')) }}
                        {{ Form::select('theme', $themes) }}
                    </div>

                    <div>
                        {{ HTML::decode(Form::label('layout', '<span class="required">*</span> Default Layout:')) }}
                        {{ Form::select('layout', $layouts) }}
                        <span id="layout_ex" class="ex"></span>
                    </div>
                </div>
            </div>

            <!-- Analytics Tab -->
            <div id="analytics-tab">
                <div class="form">
                    <div>
                        {{ HTML::decode(Form::label('ga_account_id', '<span class="required">*</span> GA Tracking Code:')) }}
                        <?php //echo form_input(array('name' => 'ga_account_id', 'id' => 'ga_account_id', 'value' => set_value('site_name', isset($Settings->ga_account_id->value) ? $Settings->ga_account_id->value : ''))); ?>
                    </div>

                    <div>
                        {{ HTML::decode(Form::label('ga_email', '<span class="required">*</span> GA Email:')) }}
                        <?php //echo form_input(array('name' => 'ga_email', 'id' => 'ga_email', 'value' => set_value('ga_email', isset($Settings->ga_email->value) ? $Settings->ga_email->value : ''))); ?>
                    </div>

                    <div>
                        {{ HTML::decode(Form::label('ga_password', '<span class="required">*</span> GA Password:')) }}
                        <?php //echo form_password(array('name' => 'ga_password', 'id' => 'ga_password', 'value' => set_value('ga_password', isset($Settings->ga_password->value) ? $Settings->ga_password->value : ''))); ?>
                    </div>

                    <div>
                        {{ HTML::decode(Form::label('ga_profile_id', '<span class="required">*</span> GA Profile ID:')) }}
                        <?php //echo form_input(array('name' => 'ga_profile_id', 'id' => 'ga_profile_id', 'value' => set_value('ga_profile_id', isset($Settings->ga_profile_id->value) ? $Settings->ga_profile_id->value : ''))); ?>
                    </div>
                </div>
            </div>
        </div>
        {{ Form::close() }}

    </div>
</div>

<script type="text/javascript">
    $(document).ready(function() {
        $( ".tabs" ).tabs();

        $('#theme').change( function() {

            $('#layout').html('');
            $('#layout_ex').html('Loading Layouts...');

            $.post('{{ Admin::url('/system/theme-layouts') }}', {theme: $('#theme').val()}, function(response) {
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