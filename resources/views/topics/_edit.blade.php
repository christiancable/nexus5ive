
<ul class="nav nav-tabs" id="topic{{$topic->id}}">
  <li role="presentation" class="active"><a href="#topic-view{{$topic->id}}">View</a></li>
  <li role="presentation"><a href="#topic-edit{{$topic->id}}">Settings</a></li>
</ul>

<div class="tab-content">
  <div role="tabpanel" class="tab-pane active" id="topic-view{{$topic->id}}">
    @include('topics._read', $topic)
  </div>
  <div role="tabpanel" class="tab-pane" id="topic-edit{{$topic->id}}">
   @include('topics._create', $topic)
  </div>
</div>