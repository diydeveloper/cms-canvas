<div class="subnav">
    <ul>
        <li><a {!! (Request::segment(4) == 'edit') ? 'class="selected"' : '' !!} href="{!! Admin::url("/user/{$user->id}/edit") !!}"><span>Edit User</span></a></li>
        <li><a {!! (Request::segment(4) == 'avatar') ? 'class="selected"' : '' !!} href="{!! Admin::url("/user/{$user->id}/avatar") !!}"><span>Update Avatar</span></a></li>
    </ul>
    <div class="clear"></div>
</div>
