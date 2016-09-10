@include('cmscanvas::admin.user.subnav')

<div class="box">
    <div class="heading">
        <h1><img alt="" src="{!! Theme::asset('images/user.png') !!}"> Update Avatar</h1>

        <div class="buttons">
        </div>
    </div>
    <div class="content">
        {!! Form::open(['id' => 'form', 'files' => true]) !!}
        <div class="form">
            <div>
                <img src="{!! $user->avatar(100, 100) !!}" />
                &nbsp;
                <a href="javascript:void(0);" id="remove_image"><span>Remove</span></a>
            </div>
            <div>
                <span class="required">*</span> Upload Image: &nbsp;
                {!! Form::file('image_upload') !!}
            </div>
        </div>
        <br />
        <div class="buttons">
            <a class="button" href="javascript:void(0);" id="save" onClick="$('#form').submit()"><span>Upload</span></a>
        </div>
        {!! Form::close() !!}

        <script type="text/javascript">
            $(document).ready(function() {
                $('#remove_image').click( function() {
                    form = $('<form>').attr({
                        method: 'post'
                    });

                    $('<input>').attr({
                        type: 'hidden',
                        name: 'remove_image',
                        value: '1'
                    }).appendTo(form);
                    
                    $('<input>').attr({
                        type: 'hidden',
                        name: '_token',
                        value: $('meta[name="csrf-token"]').attr('content')
                    }).appendTo(form);

                    form.submit();

                    return false;
                });
            });
        </script>
    </div>
</div>
