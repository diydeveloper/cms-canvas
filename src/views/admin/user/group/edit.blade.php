<div class="box">
    <div class="heading">
        <h1><img alt="" src="{{ Theme::asset('images/user.png') }}"> {{ ( ! empty($userGroup)) ? 'Edit' : 'Add' }} Group</h1>

        <div class="buttons">
            <a class="button" href="javascript:void(0);" id="save" onClick="$('#group_edit_form').submit()"><span>Save</span></a>
            <a class="button" href="{{ Admin::url('user/group') }}"><span>Cancel</span></a>
        </div>
    </div>
    <div class="content">
        @if ( ! empty($userGroup))
            {{ Form::model($userGroup, array('id' => 'group_edit_form')) }}
        @else
            {{ Form::open(array('id' => 'group_edit_form')) }}
        @endif

            <div id="edit-group-tab">
                <div class="form">
                    <div>
                        {{ HTML::decode(Form::label('name', '<span class="required">*</span> Group Name:')) }}
                        {{ Form::text('name') }}
                    </div>
                </div>
            </div>

        <div class="clear"></div>

        {{ Form::close() }}
    </div>
</div>