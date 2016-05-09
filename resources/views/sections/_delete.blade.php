<form action="{{action('Nexus\SectionController@destroy', ['id' => $subSection->id])}}" method="POST">
    {{ csrf_field() }}
    {{ method_field('DELETE') }}
<li role="presentation">{!! Form::button('<span class="glyphicon glyphicon-save"></span> Archive Section</button>', ['Type' => 'Submit', 'class' => 'btn btn-link' ]) !!}</li>
{!! Form::close() !!}