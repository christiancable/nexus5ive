<ul class="nav nav-tabs nexus-topic-nav" id="topic{{$topic->id}}">
  <li role="presentation" class="dropdown pull-right">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Settings</a>
    <ul class="dropdown-menu">      
      <li role="presentation" class="active"><a href="#topic-view{{$topic->id}}">View</a></li>
      <li role="presentation"><a href="#topic-edit{{$topic->id}}">Edit</a></li>
      <li role="separator" class="divider"></li>
      @include('topics._delete', $topic)
    </ul>
  </li>
</ul>

<div class="tab-content">
  <div role="tabpanel" class="tab-pane active" id="topic-view{{$topic->id}}">
    @include('topics._read', $topic)
  </div>
  <div role="tabpanel" class="tab-pane" id="topic-edit{{$topic->id}}">
   @include('topics._update', $topic)
 </div>
</div>
