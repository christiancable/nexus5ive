<?php
$errorBag = 'sectionUpdate' . $section->id;
$displayErrors = $errors->$errorBag->all() ? true : false;
?>
<ul class="nav nav-tabs" id="section{{$section->id}}" role="tablist">
    <li role="presentation" @if (!$displayErrors) class="active" @endif>
        <a href="#section{{$section->id}}-view" id="section{{$section->id}}-view-tab" 
            role="tab" data-toggle="tab" aria-controls="section{{$section->id}}-view" aria-expanded="true">View</a>
    </li>
    <li role="presentation" @if ($displayErrors) class="active" @endif>
        <a href="#section{{$section->id}}-settings" role="tab" 
            id="section{{$section->id}}-settings-tab" data-toggle="tab" 
            aria-controls="section{{$section->id}}-settings">Settings</a>
    </li>
</ul>

<div class="tab-content" id="section{{$section->id}}TabContent">
    <br/>

    <div class="tab-pane fade @if (!$displayErrors) in active @endif" role="tabpanel" id="section{{$section->id}}-view" aria-labelledby="section{{$section->id}}-tab">
        @include('_heading', [
            $heading = $section->title, 
            $lead = $section->intro,
            $introduction = "Moderated by: {$section->moderator->present()->profileLink}"
        ])
    </div> 

    <div class="tab-pane fade @if ($displayErrors) in active @endif" role="tabpanel" id="section{{$section->id}}-settings" aria-labelledby="settings-tab">
        @include('sections._header_edit', $section)
    </div>
</div>