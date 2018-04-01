<?php
$errorBag = 'sectionUpdate' . $subSection->id;
$showErrors = count($errors->$errorBag->all());
?>
<div class="col-md-12">

  <ul class="nav nav-tabs settings-menu" id="subSection{{$subSection->id}}" role="tablist"> 
    <li role="presentation" class="dropdown active pull-right"> 
        <a href="#" class="dropdown-toggle topic-settings" id="subSectionTabDrop{{$subSection->id}}" data-toggle="dropdown" aria-controls="subSectionTabDrop{{$subSection->id}}-contents" aria-expanded="false">
            <span class="glyphicon glyphicon-cog" aria-hidden="true"></span> Settings <span class="caret"></span>
        </a> 
        <ul class="dropdown-menu" aria-labelledby="subSectionTabDrop{{$subSection->id}}" id="subSectionTabDrop{{$subSection->id}}-contents"> 
            
            @if ($showErrors)
            <li class="">
            @else 
            <li class="active">
            @endif 
              <a href="#subSection-dropDown{{$subSection->id}}-view" role="tab" 
                id="subSection-dropDown{{$subSection->id}}-view-tab" data-toggle="tab" aria-controls="subSection-dropDown{{$subSection->id}}-view" 
                aria-expanded="false">View</a>
            </li>

            @if ($showErrors)
            <li class="active">
            @else 
            <li class="">
            @endif 
              <a href="#subSection-dropDown{{$subSection->id}}-edit" role="tab"
                id="subSection-dropDown{{$subSection->id}}-edit-tab" data-toggle="tab" aria-controls="subSection-dropDown{{$subSection->id}}-edit"
                aria-expanded="true">Edit</a>
              </li>   

            <li role="separator" class="divider"></li>
            @include('sections._delete', $subSection) 
        </ul>
    </li> 
  </ul>

  <div class="tab-content" id="subSectionTabsContent{{$subSection->id}}"> 
  
  @if ($showErrors)
      <div class="tab-pane fade " role="tabpanel" id="subSection-dropDown{{$subSection->id}}-view" 
        aria-labelledby="subSection-dropDown{{$subSection->id}}-view-tab">
        @include('sections._read', $subSection)
      </div>

      <div class="tab-pane fade active in" role="tabpanel" id="subSection-dropDown{{$subSection->id}}-edit" 
        aria-labelledby="subSection-dropDown{{$subSection->id}}-edit-tab"> 
        @include('sections._edit', $subSection)
      </div> 
  @else 
      <div class="tab-pane fade active in" role="tabpanel" id="subSection-dropDown{{$subSection->id}}-view" 
        aria-labelledby="subSection-dropDown{{$subSection->id}}-view-tab">
        @include('sections._read', $subSection)
      </div>

      <div class="tab-pane fade " role="tabpanel" id="subSection-dropDown{{$subSection->id}}-edit" 
        aria-labelledby="subSection-dropDown{{$subSection->id}}-edit-tab"> 
        @include('sections._edit', $subSection)
      </div> 
  @endif 

  </div> 

</div>
