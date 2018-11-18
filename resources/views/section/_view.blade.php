<?php

?>
<div class="card">
  <div class="card-header">
    <h2 class="card-title"><a href="{{ action('Nexus\SectionController@show', ['id' => $subSection->id])}}">{{$subSection->title}}</a></h2>
  </div>
  <div class="card-body">
    <h5 class="card-title">{!! App\Helpers\NxCodeHelper::nxDecode($subSection->intro)  !!}</h5>
    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>
  </div>
  <footer>
  @if($subSection->topicCount || $subSection->sectionCount)
                    <hr class="visible-xs"/>
                    <p class="small text-muted">
                        @if($subSection->topicCount)
                            {{$subSection->topicCount}} 
                            @if($subSection->topicCount > 1)
                                topics 
                            @else
                                topic
                            @endif
                        @endif            

                        @if($subSection->topicCount && $subSection->sectionCount)
                            and
                        @endif

                        @if($subSection->sectionCount)
                            {{$subSection->sectionCount}} 
                            @if($subSection->sectionCount > 1)
                                sections 
                            @else
                                section
                            @endif
                        @endif
                @endif

                @if($subSection->most_recent_post)
                    <br/>
                    Latest Post in <a href="{{ action('Nexus\TopicController@show', ['id' => $subSection->most_recent_post->topic->id])}}">{{$subSection->most_recent_post->topic->title}}</a>, {{$subSection->most_recent_post->time->diffForHumans()}}</p> 
                @endif
  </footer>
</div>
