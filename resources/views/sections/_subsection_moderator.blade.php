<div class="col-md-12">


<ul class="nav nav-tabs nexus-topic-nav" id="section{{$subSection->id}}">
  <li role="presentation" class="dropdown pull-right">
    <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false"><span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Settings</a>
    <ul class="dropdown-menu">      
      <li role="presentation" class="active"><a href="#section-view{{$subSection->id}}">View</a></li>
      <li role="presentation"><a href="#section-edit{{$subSection->id}}">Edit</a></li>
      <li role="separator" class="divider"></li>
      @include('sections._delete', $subSection) 
    </ul>
  </li>
</ul>

<div class="tab-content">
  <div role="tabpanel" class="tab-pane active" id="section-view{{$subSection->id}}">
    @include('sections._read', $subSection)
  </div>
  <div role="tabpanel" class="tab-pane" id="section-edit{{$subSection->id}}">
   @include('sections._edit', $subSection)
 </div>
</div>

</div>



