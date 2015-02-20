<?php $errors = (array)Session::get('error'); ?>

@foreach ($errors as $error)
    <p class="notification error">{{ $error }}<span class="dropdown_close"></span></p>
@endforeach

<?php $notices = (array)Session::get('notice'); ?>

@foreach ($notices as $notice)
    <p class="notification attention">{{ $notice }}<span class="dropdown_close"></span></p>
@endforeach

<?php $messages = (array)Session::get('message'); ?>

@foreach ($messages as $message)
    <p class="notification success">{{ $message }}<span class="dropdown_close"></span></p>
@endforeach