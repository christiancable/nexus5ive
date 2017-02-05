<a href="{{action('Nexus\UserController@show', ['user_name' => $user->username]) }}">
    <div class="panel panel-primary panel-user">
       <div class="panel-heading">
           <h3 class="panel-title clearfix">
               <span class="text-muted">@</span><strong>{{$user->username}}</strong> <em class="pull-right">{{$user->name}}</em>
           </h3>
       </div>
       <div class="panel-body">
          @if($user->popname)
          {{$user->popname}}
          @endif 
      </div>

      @if ($user->latestLogin)
      <div class="panel-footer clearfix">
          <span class="pull-right">Latest Visit {{$user->latestLogin->diffForHumans()}}</span>
      </div>
      @endif
  </div>
</a>

