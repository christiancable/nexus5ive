<?php
    $today = \Carbon\Carbon::now();
    $halloween = \Carbon\Carbon::create($today->year, 10, 31);
    $isItTime = $halloween->isSameDay($today);
?>
@if ($isItTime)
    <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/W7-uC0LDllM?rel=0" frameborder="0" allowfullscreen></iframe>
@else 
    <code>
    You will not be saved by <br/>
    the Holy Ghost.
    </code>
    <br/><br/>
    <code>
    You will not be saved by<br/>
    the god Plutonium.
    </code>
    <br/><br/>
    <code>
    In fact,<br/>
    <strong>YOU WILL NOT BE SAVED!</strong>
    </code>
@endif 
