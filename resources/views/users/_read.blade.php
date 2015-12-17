
                <div class="row">
                <dl class="dl-horizontal col-md-6">        
                    <dt>Name</dt><dd>{{$user->name}}</dd>

                    @if ($user->private != true)
                        <dt>Email</dt><dd><a href="mailto:{{$user->email}}">{{$user->email}}</a></dd>
                    @else
                        <dt>Email</dt><dd><em>Hidden</em></dd>
                    @endif

                    <dt>Popname</dt><dd>{{$user->popname}}</dd>
                    @if ($user->latestLogin)
                    	<dt>Latest Visit</dt><dd>{{$user->latestLogin->diffForHumans()}}</dd>
                    @else
                    	<dt>Latest Visit</dt><dd>Never</dd>
                    @endif
                    
                </dl>

                <dl class="dl-horizontal col-md-6">        
                    <dt>Location</dt><dd>{{$user->location}}</dd>
                    <dt>Favourite Film</dt><dd>{{$user->favouriteMovie}}</dd>
                    <dt>Favourite Band</dt><dd>{{$user->favouriteMusic}}</dd>

                    <dt>Total Posts</dt><dd>{{$user->totalPosts}}</dd>
                    <dt>Total Visits</dt><dd>{{$user->totalVisits}}</dd>


                </dl>
                </div>

                <div class="well">{!! nl2br($user->about) !!}</div>
                @if (count($user->sections))
     {{--                <h2>Sections</h2> --}}
                    <span>If you like <strong>{{$user->username}}</strong> then check out these sections they moderate </span>
                    <!-- Single button -->
                    <div class="btn-group">
                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        Choose Section <span class="caret"></span>
                      </button>
                      <ul class="dropdown-menu">
                        @foreach ($user->sections as $section)
                            <li><a href="{{ action('Nexus\SectionController@show', ['section_id' => $section->section_id]) }}">{{$section->section_title}}</a></li>
                        @endforeach
                      </ul>
                    </div>

                   <hr> 
                @endif