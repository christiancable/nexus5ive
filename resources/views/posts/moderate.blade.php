<ul class="nav nav-tabs settings-menu" id="post{{$post->id}}" role="tablist"> 
    <li role="presentation" class="dropdown active pull-right"> 
        <a href="#" class="dropdown-toggle post-settings" id="postTabDrop{{$post->id}}" data-toggle="dropdown" aria-controls="postTabDrop{{$post->id}}-contents" aria-expanded="false">
            <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Settings <span class="caret"></span>
        </a> 
        <ul class="dropdown-menu" aria-labelledby="postTabDrop{{$post->id}}" id="postTabDrop{{$post->id}}-contents"> 
            <li class="active"><a href="#dropDown{{$post->id}}-view" role="tab" id="dropDown{{$post->id}}-view-tab" data-toggle="tab" aria-controls="dropDown{{$post->id}}-view" aria-expanded="false">View</a></li>
            <li class=""><a href="#dropDown{{$post->id}}-edit" role="tab" id="dropDown{{$post->id}}-edit-tab" data-toggle="tab" aria-controls="dropDown{{$post->id}}-edit" aria-expanded="true">Edit</a></li> 
            @if(!isset($hideDelete))
              <li role="separator" class="divider"></li>
              @include('posts._delete', $post)
            @endif
        </ul>
    </li> 
</ul>

<div class="tab-content" id="postTabsContent{{$post->id}}"> 
    <div class="tab-pane fade active in" role="tabpanel" id="dropDown{{$post->id}}-view" aria-labelledby="dropDown{{$post->id}}-view-tab">
        @include('posts.show', $post)
    </div>

    <div class="tab-pane fade " role="tabpanel" id="dropDown{{$post->id}}-edit" aria-labelledby="dropDown{{$post->id}}-edit-tab"> 
        @include('posts._edit', $post)
    </div> 
</div> 