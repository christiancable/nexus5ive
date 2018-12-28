<ul class="nav nav-pills justify-content-end mb-3">
  
  <li class="nav-item dropdown">
    <a class="nav-link dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
      <span class="oi oi-cog"></span><span class="sr-only">Settings</span>
    </a>

    <div class="dropdown-menu">

      <a class="dropdown-item" 
        href="{{$viewTabLink}}" id="{{$viewTabId}}" 
        data-toggle="tab" role="tab" aria-controls="{{$viewTabId}}">View</a>

      <a class="dropdown-item"
        href="{{$editTabLink}}" id="{{$editTabId}}" 
        data-toggle="tab" role="tab" aria-controls="{{$editTabId}}">Edit</a>
      
    </div>
  </li>
  
</ul>
