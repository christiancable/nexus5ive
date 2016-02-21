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
    @include('posts._edit',$topic)
    <?php $tabGroups[] = $topic->id ?>
  </div>

  <div role="tabpanel" class="tab-pane" id="preview">
    <div class="panel panel-info">
        <div class="panel-heading">
            <h3 class="panel-title" id='preview-title'>&nbsp;</h3>
        </div>
        <div class="panel-body">
            <p id='preview-view'>&hellip; hold on a second &hellip;</p>
        </div>
    </div>
 </div>
</div>

    <div class="row">

        <div class="col-md-2">
            <div class="form-group">
            {!! Form::submit('Add Comment', ['class'=> 'btn btn-primary form-control']) !!}
            </div>
        </div>

        <div class="col-md-10">
            <p  class="pull-right small text-muted" data-toggle="popover" data-html="true" title="Formating Help" data-placement="left" data-content="{!! Nexus\Helpers\BoilerplateHelper::formattingHelp() !!}"><u>Formatting Help</u></p>
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
@if (isset($tabGroups))
    @include('javascript._postPreviewTabs', $tabGroups)
@endif
@endsection


