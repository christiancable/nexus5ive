<!DOCTYPE html>
<html>
    <head>
        <title>Laravel</title>

        <link href="//fonts.googleapis.com/css?family=Lato:100" rel="stylesheet" type="text/css">

        <style>
            html, body {
                height: 100%;
            }

            body {
                margin: 0;
                padding: 0;
                width: 100%;
                display: table;
                font-weight: 100;
                font-family: 'Lato';
            }

            .container {
                text-align: center;
                display: table-cell;
                vertical-align: middle;
            }

            .content {
                text-align: center;
                display: inline-block;
            }

            .title {
                font-size: 96px;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="content">
                <div class="title">{{$user->user_name}}</div>
                <h2>User Information</h2>
                <dl>
                    {{-- <dt>user_id</dt><dd>{{$user->user_id}}</dd> --}}
                {{--     <dt>user_name</dt><dd>{{$user->user_name}}</dd> --}}
                  {{--   <dt>Activity</dt><dd>{{$user->user_location}}</dd> --}}
               {{--      <dt>user_password</dt><dd>{{$user->user_password}}</dd> --}}
                    {{-- <dt>user_priv</dt><dd>{{$user->user_priv}}</dd>
                    <dt>user_display</dt><dd>{{$user->user_display}}</dd>
                    <dt>user_backwards</dt><dd>{{$user->user_backwards}}</dd>
                    <dt>user_sysop</dt><dd>{{$user->user_sysop}}</dd>
                    <dt>user_theme</dt><dd>{{$user->user_theme}}</dd> --}}
                    {{-- <dt>user_status</dt><dd>{{$user->user_status}}</dd> --}}
                    {{-- <dt>user_banned</dt><dd>{{$user->user_banned}}</dd> --}}
                    {{-- <dt>user_ipaddress</dt><dd>{{$user->user_ipaddress}}</dd> --}}
                    {{-- <dt>user_no_pictures</dt><dd>{{$user->user_no_pictures}}</dd> --}}
                    {{-- <dt>mojo</dt><dd>{{$user->mojo}}</dd> --}}
                    {{-- <dt>user_hideemail</dt><dd>{{$user->user_hideemail}}</dd> --}}
                    

                    <dt>Name</dt><dd>{{$user->user_realname}}</dd>

                    @if ($user->user_hideemail === 'no')
                        <dt>Email</dt><dd>{{$user->user_email}}</dd>
                    @else
                        <dt>Email</dt><dd>Hidden</dd>
                    @endif

                    <dt>Popname</dt><dd>{{$user->user_popname}}</dd>
                    <dt>Age</dt><dd>{{$user->user_age}}</dd>
                    <dt>Sex</dt><dd>{{$user->user_sex}}</dd>
                    <dt>Location</dt><dd>{{$user->user_town}}</dd>
                    
                    <dt>Further Information</dt><dd>{{$user->user_comment}}</dd>

                    <dt>Total Post</dt><dd>{{$user->user_totaledits}}</dd>
                    <dt>Total Visits</dt><dd>{{$user->user_totalvisits}}</dd>

                    <dt>Favourite Film</dt><dd>{{$user->user_film}}</dd>
                    <dt>Favourite Band</dt><dd>{{$user->user_band}}</dd>

                </dl>
                <h2>Comments</h2>
                <ul>
                @foreach ($user->comments as $comment)
                    <li><strong>{{$comment->author->user_name}}</strong> - {{$comment->text}}</li>
                @endforeach
                </ul>
                {{-- {{ dd($user->comments)}} --}}
            </div>
        </div>
    </body>
</html>
