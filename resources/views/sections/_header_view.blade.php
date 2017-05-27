<div class="container">
    <h1>{{$section->title}}</h1>
    <span class="lead">{!! App\Helpers\NxCodeHelper::nxDecode($section->intro) !!}</span>
    <p>Moderated by: {!! $section->moderator->present()->profileLink !!}
</div>
    


 

