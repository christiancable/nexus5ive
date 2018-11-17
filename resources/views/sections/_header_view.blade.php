<h1 class="display-4">{{$section->title}}</h1>
<p class="lead">{!! App\Helpers\NxCodeHelper::nxDecode($section->intro) !!}</p>
<p>Moderated by: {!! $section->moderator->present()->profileLink !!}
