@if($topic->readonly) 
  <div class="alert alert-warning" role="alert">
      <p><strong>This topic is closed</strong> but you are allowed to post because you can moderate this section.</p>
  </div>
@endif 

<?php $help = App\Helpers\BoilerplateHelper::formattingHelp();?>
<div id="app" v-cloak>
  <post-compose
    :topic="{{json_encode($topic)}}"
    help="{{$help}}"
    >
  </post-compose>
</div>



