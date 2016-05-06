<div class="content">
    <h1>{{$section->title}}</h1>
    <p class="lead">{{$section->intro}}</p>
    <p>Moderated by: <a href="{{ action('Nexus\UserController@show', ['username' => $section->moderator->username])}}">{{$section->moderator->username}}</a></p>
</div>