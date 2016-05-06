<div class="content">
    <h1>{{$section->title}}</h1>
    <span class="lead">{!! Nexus\Helpers\NxCodeHelper::nxDecode($section->intro) !!}</span>
    <p>Moderated by: <a href="{{ action('Nexus\UserController@show', ['username' => $section->moderator->username])}}">{{$section->moderator->username}}</a></p>
</div>