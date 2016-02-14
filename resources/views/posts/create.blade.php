@if($topic->readonly) 
<div class="alert alert-warning" role="alert">
    <p><strong>This topic is closed</strong> but you are allowed to post because you can moderate this section.</p>
</div>
@else 
@endif

{!! Form::open(['url' => 'posts']) !!}


{!! Form::hidden('topic_id', $topic->id) !!}

    <div class="form-group">
        {!! Form::label('title', 'Subject') !!}
        {!! Form::text('title', null, ['class'=> 'form-control']) !!}
    </div>

    <div class="form-group">
{{--         {!! Form::label('message_text', 'Subject') !!} --}}
        {!! Form::textarea('text', null, ['class'=> 'form-control']) !!}
    </div>


    <div class="row">

        <div class="col-md-2">
            <div class="form-group">
            {!! Form::submit('Add Comment', ['class'=> 'btn btn-primary form-control']) !!}
            </div>
        </div>


<?php
$helpText = <<<TEXT
<strong>Basics</strong>
<pre>
some **bold** text
</pre>
<pre>
some _italics_
</pre>
<hr/>
<strong>Links</strong>
<pre>
Here is a link [click here](https://nexus5.org.uk).
</pre>
or just paste in the address and it will be clickable
<hr/>
<strong>Images</strong>
<pre>
![Look a picture](http://example.com/picture.jpg)
</pre>
or
<pre>
[picture-]http://example.com/picture.jpg[-picture]
</pre>
<hr/>
<strong>Lists</strong>
<pre>
Star Treks:

- Original Series
- Next Generation
- Deep Space Nine
- Voyager
- Enterprise
</pre>
TEXT;
?>
        <div class="col-md-10">
            <p  class="pull-right" data-toggle="popover" data-html="true" title="Formating Help" data-placement="left" data-content="{!! $helpText !!}">Formatting Help</p>
        </div>

</div>
{!! Form::close() !!}

{{-- the only error we have is if the user tries to leave a blank comment --}}
@if ($errors->any())
    <p class="alert alert-danger">
        Only a monster would try to leave an empty comment! 
    </p>
@endif 

@section('javascript')
<script>
$(function () {
  $('[data-toggle="popover"]').popover()
})
</script>
@endsection


