 <?php
 $errorBag = 'topicUpdate' . $topic->id;
    ?>
        

<ul class="nav nav-tabs settings-menu" id="topic{{$topic->id}}" role="tablist"> 
  <li role="presentation" class="dropdown active pull-right"> 
      <a href="#" class="dropdown-toggle topic-settings" id="topicTabDrop{{$topic->id}}" data-toggle="dropdown" aria-controls="topicTabDrop{{$topic->id}}-contents" aria-expanded="false">
          <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Settings <span class="caret"></span>
      </a> 
      <ul class="dropdown-menu" aria-labelledby="topicTabDrop{{$topic->id}}" id="topicTabDrop{{$topic->id}}-contents"> 
        @if ($errors->$errorBag->all())    
          <li class=""><a href="#dropDown{{$topic->id}}-view" role="tab" id="dropDown{{$topic->id}}-view-tab" data-toggle="tab" aria-controls="dropDown{{$topic->id}}-view" aria-expanded="false">View</a></li>
          <li class="active"><a href="#dropDown{{$topic->id}}-edit" role="tab" id="dropDown{{$topic->id}}-edit-tab" data-toggle="tab" aria-controls="dropDown{{$topic->id}}-edit" aria-expanded="true">Edit</a></li>   
        @else 
          <li class="active"><a href="#dropDown{{$topic->id}}-view" role="tab" id="dropDown{{$topic->id}}-view-tab" data-toggle="tab" aria-controls="dropDown{{$topic->id}}-view" aria-expanded="false">View</a></li>
          <li class=""><a href="#dropDown{{$topic->id}}-edit" role="tab" id="dropDown{{$topic->id}}-edit-tab" data-toggle="tab" aria-controls="dropDown{{$topic->id}}-edit" aria-expanded="true">Edit</a></li>   
        @endif 
          <li role="separator" class="divider"></li>
            @include('topics._delete', $topic)
      </ul>
  </li> 
</ul>

<div class="tab-content" id="topicTabsContent{{$topic->id}}"> 
    @if ($errors->$errorBag->all())
        <div class="tab-pane fade" role="tabpanel" id="dropDown{{$topic->id}}-view" aria-labelledby="dropDown{{$topic->id}}-view-tab">
            @include('topics._read', $topic)
        </div>

        <div class="tab-pane active fade in" role="tabpanel" id="dropDown{{$topic->id}}-edit" aria-labelledby="dropDown{{$topic->id}}-edit-tab"> 
            @include('topics._update', $topic)
        </div> 
    
    @else 
        <div class="tab-pane fade active in" role="tabpanel" id="dropDown{{$topic->id}}-view" aria-labelledby="dropDown{{$topic->id}}-view-tab">
            @include('topics._read', $topic)
        </div>

        <div class="tab-pane fade " role="tabpanel" id="dropDown{{$topic->id}}-edit" aria-labelledby="dropDown{{$topic->id}}-edit-tab"> 
            @include('topics._update', $topic)
        </div> 
    @endif  
</div> 