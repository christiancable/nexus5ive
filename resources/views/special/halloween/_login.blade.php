<?php
  $today = \Carbon\Carbon::now();
  $halloween = \Carbon\Carbon::create($today->year, 10, 31, 0);
  $daysToHalloween = $today->diffInDays($halloween, false);
?>
@if ($daysToHalloween == 0)
    <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/hIHUv2ooG38?rel=0" frameborder="0" allowfullscreen></iframe>
@else 
    <p class='lead'>This is not a dream... not a dream.</p>

    <p>We are using your brain's electrical system as a <em>receiver</em>.</p>

    <p>We are unable to transmit through conscious neural interference. You are receiving this broadcast as a dream.</p>

    <p>We are transmitting from the year <strong>two, zero, one, six</strong>. You are receiving this broadcast in order to alter the events you are seeing. Our technology has not developed a transmitter strong enough to reach your conscious state of awareness, but this is not a dream. You are seeing what is actually occurring for the purpose of causality violation.</p>
@endif 
