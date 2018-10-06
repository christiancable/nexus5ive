<?php
    $today = \Carbon\Carbon::now();
    $halloween = \Carbon\Carbon::create($today->year, 10, 31);
    $isItTime = $halloween->isSameDay($today);
?>
@if ($isItTime)
    <iframe width="560" height="315" src="https://www.youtube-nocookie.com/embed/W7-uC0LDllM?rel=0" frameborder="0" allowfullscreen></iframe>
@else 
    <span class="plutonium">
    
    <p>You will not be saved by</p>
    
    <p>the Holy Ghost.<br>
    You will not be saved by</p>
    
    <p>the god Plutonium.<br/>
    In fact,</p>
    
    <p>YOU WILL NOT BE SAVED!</p>
    
    <span>
@endif 
