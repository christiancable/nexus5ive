@if($topic->readonly) 
<div class="alert alert-warning" role="alert">
    <p><strong>This topic is closed</strong> but you are allowed to post because you can moderate this section.</p>
</div>
@else 
@endif

<ul class="nav nav-tabs" id="{{$topic->id}}">
    <li class="active"><a href="#edit">Compose</a></li>
    <li><a href="#preview">Preview</a></li>
</ul>

<div class="tab-content">
<br/>
  <div role="tabpanel" class="tab-pane active" id="edit">
    <?php unset($tabGroups); ?>
    @include('posts._compose',$topic)
    <?php $tabGroups[] = $topic->id ?>
  </div>

  <div role="tabpanel" class="tab-pane" id="preview">
    @include('posts._preview')
 </div>
</div>

<?php
  $formattingHelp = Nexus\Helpers\BoilerplateHelper::formattingHelp();
?>
<div class="row">
    
    <div class="col-md-2">
        <div class="form-group">
            {!! Form::submit('Add Comment', ['class'=> 'btn btn-primary form-control']) !!}
        </div>
    </div>

    <div class="col-md-10">
      {{-- formatting help for larger screens --}}
      <p class="pull-right small text-muted visible-md visible-lg" data-toggle="popover" data-html="true" title="Formating Help" data-placement="left" data-content="{!! $formattingHelp !!}"><u>Formatting Help</u></p>
      
      {{-- formatting help for small screens --}}
      <div class="visible-xs visible-sm small">
        <div class="panel-group" id="accordion" role="tablist" aria-multiselectable="true">
          <div class="panel panel-default">
            <div class="panel-heading" role="tab" id="formattinghelp">  
              <a class="collapsed" role="button" data-toggle="collapse" data-parent="#accordion" href="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                <span class='glyphicon  glyphicon-triangle-right'></span> Formatting Help
              </a>
            </div>
            <div id="collapseTwo" class="panel-collapse collapse" role="tabpanel" aria-labelledby="addNewTopic">
              <div class="panel-body">
                <p>{!! $formattingHelp !!}</p>
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

</div>
{!! Form::close() !!}
{{-- the only error we have is if the user tries to leave a blank comment --}}
@if ($errors->post->any())
    <p class="alert alert-danger">
        Only a monster would try to leave an empty comment! 
    </p>
@endif 

@section('javascript')
@parent
{!! var_dump($tabGroups) !!}
<script>
$(function () {
  $('[data-toggle="popover"]').popover()
})
</script>
@if (isset($tabGroups))
    @include('javascript._postPreviewTabs', $tabGroups)
@endif
@endsection


