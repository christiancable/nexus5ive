<?php
  $today = \Carbon\Carbon::now();
  $halloween = \Carbon\Carbon::create($today->year, 10, 31, 0);
  $daysToHalloween = $today->diffInDays($halloween, false);
?>
@if ($daysToHalloween > 0) 
  <p class="lead">{{$daysToHalloween}} more days to Halloween, Halloween, Halloween... Silver Shamrock!</p>
@else
  @if ($daysToHalloween == 0)
    <p class="lead">Hurry up it's Halloween, Halloween, Halloween... Silver Shamrock!</p>
  @else 
    <p class="lead">Silver Shamrock!!</p>
  @endif 
@endif