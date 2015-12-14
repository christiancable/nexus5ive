@extends('layouts.master')

@section('meta')
<title>{{$user->username}}</title>
@endsection

@section('content')
        <div class="container">
            <div class="content">
                <h1>{{$user->username}}</h1>
                <hr>
                @if ($errors->all())
                <div class="row">
                <ul>
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                     @endforeach
                 </ul>
                </div>
                @endif

{!! 
Form::model($user, array(
    'route' => ['user.update', $user->name],
    'class' => 'form')) 
!!}

<div class="row form-group">

        <dl class="dl-horizontal col-md-6">        
            <dt>{!! Form::label('name','Name') !!}</dt>
            <dd>{!! Form::text('name', null, ['class'=> 'form-control'])!!}</dd>

            <dt>{!! Form::label('email','Email') !!}</dt>
            <dd>{!! Form::text('email', null, ['class'=> 'form-control'])!!}</dd>


            <dt>{!! Form::label('popname','Popname') !!}</dt>
            <dd>{!! Form::text('popname', null, ['class'=> 'form-control'])!!}</dd>      

	    <dt>{!! Form::label('private','Hide Email AddressXSXC') !!}</dt>
            <dd>{!! Form::checkbox('private', null, ['class'=> 'form-control'])!!}</dd>

        </dl>

        <dl class="dl-horizontal col-md-6">        
            <dt>Location</dt>
            <dd>{!! Form::text('location', null, ['class'=> 'form-control'])!!}</dd>

            <dt>{!! Form::label('favouriteMovie','Favourite Film') !!}</dt>
            <dd>{!! Form::text('favouriteMovie', null, ['class'=> 'form-control'])!!}</dd>

            <dt>{!! Form::label('favouriteMusic','Favourite Band') !!}</dt>
            <dd>{!! Form::text('favouriteMusic', null, ['class'=> 'form-control'])!!}</dd>



        </dl>
    </div>

    <div class="row text-muted">
    
        <dl class="dl-horizontal col-md-6">        
            @if ($user->latestLogin)
            <dt>Latest Visit</dt><dd>{{$user->latestLogin->diffForHumans()}}</dd>
            @else
            <dt>Latest Visit</dt><dd>Never</dd>
            @endif
        </dl>
        
        <dl class="dl-horizontal col-md-6">        
            <dt>Total Posts</dt><dd>{{$user->totalPosts}}</dd>
            <dt>Total Visits</dt><dd>{{$user->totalVisits}}</dd>
        </dl>
    
    </div>

 {!! Form::textarea('about', null, ['class'=> 'form-control']) !!}
            
                <div class="form-group">
        {!! Form::submit('Update', ['class'=> 'btn btn-warning form-control']) !!}
    </div>
{!! Form::close() !!}

                @if (count($user->sections))
     {{--                <h2>Sections</h2> --}}
                    <span>You moderate the following sections </span>
                    <!-- Single button -->
                    <div class="btn-group">
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Choose Section <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu">
                        @foreach ($user->sections as $section)
                            <li><a href="{{ action('Nexus\SectionController@show', ['section_id' => $section->section_id]) }}">{{$section->section_title}}</a></li>
                        @endforeach
                      </ul>
                    </div>

                   <hr> 
                @endif

                <h2>Comments</h2>
                @include('comments.create', $user)
                @if (count($user->comments))
                    <ul>
                    @foreach ($user->comments as $comment)
                        @include('comments.show', $comment)
                    @endforeach
                @endif
                </ul>
            </div>
        </div>
@endsection
