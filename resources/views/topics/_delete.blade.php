<form action="{{action('Nexus\TopicController@destroy', ['id' => $topic->id])}}" method="POST">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
<li role="presentation">{!! Form::button('<span class="glyphicon glyphicon-save"></span> Archive Topic</button>', ['Type' => 'Submit', 'class' => 'btn btn-link' ]) !!}</li>
{!! Form::close() !!}