@extends('theme::master')

@section('content')
    <div id="content">
        <div class="breadcrumb">@include('theme::partials.breadcrumbs')</div>

        @if (empty($disableNotifications))
            @include('theme::partials.notifications')
        @endif

        {{ $content }}
    </div>
@stop