<div class="box">
    <div class="heading">
        <?php if ( ! empty($navigation)): ?>
            <h1><img alt="" src="{!! Theme::asset('images/category.png') !!}">Edit Navigation - {!! $navigation->title !!}</h1>
        <?php else: ?>
            <h1><img alt="" src="{!! Theme::asset('images/category.png') !!}">Add Navigation</h1>
        <?php endif; ?>

        <div class="buttons">
            <a class="button" href="javascript:void(0);" onClick="$('#form').submit();"><span>Save</span></a>
            <a class="button" href="{!! Admin::url('content/navigation') !!}"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">
        {!! Form::model($navigation, array('id' => 'form')) !!}
        <div>
            <div class="form">
                <div>
                    <label for="title"><span class="required">*</span> Title:</label>
                    {!! Form::text('title') !!}
                </div>
                <div>
                    <label for="short_name"><span class="required">*</span> Short Name:</label>
                    {!! Form::text('short_name') !!}
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
</div>