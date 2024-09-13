<?php
$breadcrumbs = \App\Helpers\BreadcrumbHelper::breadcrumbForTopic($result->topic);

$linkText = '';
foreach ($breadcrumbs as $crumb) {
    $linkNames[] = $crumb['title'];
}
$linkText = implode(' > ', $linkNames);
?>

<div class="card mb-3">
    <div class="card-body">
        <p class="mb-0" ><small><a href="{!! App\Helpers\TopicHelper::routeToPost($result) !!}">{{ $linkText }}</a></small></p>
        <a href="{!! App\Helpers\TopicHelper::routeToPost($result) !!}">            
            @if ($result->topic->secret)
                <strong>Anonymous</strong>
            @else
                <strong>{{$result->author->username}}</strong>
            @endif

            @if (!empty($result->title))
                wrote about <em>{{$result->title}}</em>
            @endif

            in <strong>{{ $result->topic->title}}</strong>
        </a>
        <span class="text-muted"> {{ $result->time->diffForHumans() }}</span>
        <p class="card-text text-muted">
            {!! App\Helpers\NxCodeHelper::nxDecode($result->text) !!}
        </p>
    </div>
</div>
