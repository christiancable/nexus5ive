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
                <div class="title">Sections</div>
                <ul>
                @foreach ($sections as $section)
                    <li>
                    <h2>{{$section->section_title}}</h2>
                    <p>{{$section->section_intro}}</p>
                    </li>

           {{--          <li>
                    <a href="{{ url("/{$section->section_title}") }}">{{$section->section_title}}</a>
                    <p>{{$section->section_intro}}</p>
                    <!-- <p>Parent Section is : {{$section->parent->section_title}}</p> -->
                    </li>                --}}
                @endforeach
                </ul>
            </div>
        </div>
    </body>
</html>
