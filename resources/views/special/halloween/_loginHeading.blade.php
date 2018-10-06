<?php
  $today = \Carbon\Carbon::now();
  $halloween = \Carbon\Carbon::create($today->year, 10, 31, 0);
  $daysToHalloween = 1 + $today->diffInDays($halloween, false);
  $isItTime = $halloween->isSameDay($today);
?>
@if (true == $isItTime) 
    <p class="lead">Hurry up it's Halloween, Halloween, Halloween... Silver Shamrock!</p>
@else     
    @if ($daysToHalloween > 0)
      <p class="lead"><span class="spooky">{{$daysToHalloween}}</span> more 
        @if ($daysToHalloween == 1)
           day
        @else
          days 
        @endif 
      to Halloween, Halloween, Halloween... <span class="spooky">Silver Shamrock!</span></p>
    @else
        <p class="lead">Silver Shamrock!!</p>
    @endif
@endif