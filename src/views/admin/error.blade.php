<div class="breadcrumb"></div>
<div class="box">
    <div class="heading">
      <h1><img src="{{ Theme::asset('images/error.png') }}" alt="" /> {{ $exception->getHeading() }}</h1>
    </div>
    <div class="content">
        <div style="border: 1px solid #DDDDDD; background: #F7F7F7; text-align: center; padding: 15px;">
            {{ $exception->getMessage() }}
        </div>
    </div>
</div>
