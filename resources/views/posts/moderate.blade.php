<ul class="nav nav-tabs nexus-topic-nav" id="post{{$post->id}}">
  <li role="presentation" class="dropdown pull-right">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Settings</a>
    <ul class="dropdown-menu">      
      <li role="presentation" class="active"><a href="#post-view{{$post->id}}">View</a></li>
      <li role="presentation"><a href="#post-edit{{$post->id}}">Edit</a></li>
      @if(!isset($hideDelete))
      <li role="separator" class="divider"></li>
      @include('posts._delete', $post)
      @endif
    </ul>
  </li>
</ul>

<div class="tab-content">
  <div role="tabpanel" class="tab-pane active" id="post-view{{$post->id}}">
    @include('posts.show', $post)
  </div>
  <div role="tabpanel" class="tab-pane" id="post-edit{{$post->id}}">
   @include('posts._edit', $post)
 </div>
</div>



