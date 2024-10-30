@if ($flash = Session::get('headerAlert'))
<div class="container">
    <div class="alert alert-{{$flash['level']}}" role="alert">
        {!! App\Helpers\NxCodeHelper::nxDecode($flash['body']) !!}
    </div>
</div>
@endif