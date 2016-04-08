{!! Form::open(['url' => 'posts']) !!}


{!! Form::hidden('topic_id', $topic->id) !!}

    <div class="form-group">
        {!! Form::text('title', null, ['class'=> 'form-control', 'placeholder'=>'Subject']) !!}
    </div>

    <div class="form-group">
        {!! Form::textarea('text', null, ['class'=> 'form-control', 'id'=>'postText']) !!}
    </div>


