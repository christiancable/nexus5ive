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

                @if($section->parent)
                    <p>Return to <a href="{{ url("/{$section->parent->section_id}") }}">{{$section->parent->section_title}}</a><p>
                @endif 

                <div class="title">{{$section->section_title}}</div>
                

                @if (count($section->sections))
                    <h2>Sections</h2>
                    <ul>
                    @foreach ($section->sections as $subSection)
                        <li>
                        <h3><a href="{{ url("/{$subSection->section_id}") }}">{{$subSection->section_title}}</a></h3>
                        <p>{{$subSection->section_intro}}</p>
                        </li>
                    @endforeach
                    </ul>
                @endif

            </div>
        </div>
    </body>
</html>
