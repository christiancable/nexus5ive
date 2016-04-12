<form action="{{action('Nexus\PostController@destroy', ['id' => $post->id])}}" method="POST">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
<li role="presentation">{!! Form::button('<span class="glyphicon glyphicon-trash"></span> Delete</button>', ['Type' => 'Submit', 'class' => 'btn btn-link' ]) !!}</li>
{!! Form::close() !!}