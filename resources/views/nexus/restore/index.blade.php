@extends('nexus.layouts.master')

@section('meta')
    <title>Restore</title>
@endsection

@section('breadcrumbs')
    @include('nexus._breadcrumbs', $breadcrumbs)
@endsection

@section('content')

    <div class="container">

        <x-heading 
            heading="Your Archive"
            lead="_But you know what? It's never too late to get it back._"
            introduction='These are your archived sections and topics. They only visible to you until you restore them.'
            >
            <x-slot:icon>
                <x-heroicon-s-archive-box class="icon_large me-1" aria-hidden="true" />
            </x-slot>
        </x-heading>

    
        @if ($destinationSections->count() == 0)
            <div class="alert alert-warning">
                You cannot restore any sections or topics because you do not moderate any place to restore them to. Sorry!
            </div>
        @else
            <h2>Archived Sections</h2>

            <x-button class="btn-success col-12" data-bs-toggle="collapse" data-bs-target="#sections" aria-expanded="false"
                aria-controls="sections">
                <x-heroicon-s-chevron-right class="icon_mini me-2 collapse-icon" aria-hidden="true" />
                View Sections to Restore
            </x-button>

            <div class="collapse" id="sections">
                @if ($trashedSections->count() != 0)
                    @foreach ($trashedSections as $section)
                        @include('nexus.restore.section', $section)
                    @endforeach
                @else
                    <div class="alert alert-info my-3">
                        You don't have any sections to restore.
                    </div>
                @endif
            </div>

            <hr>

            <h2>Archived Topics</h2>

            <x-button class="btn-success col-12" data-bs-toggle="collapse" data-bs-target="#topics" aria-expanded="false"
                aria-controls="sections">
                <x-heroicon-s-chevron-right class="icon_mini me-2 collapse-icon" aria-hidden="true" />
                View Topics to Restore
            </x-button>

            <div class="collapse" id="topics">
                @if ($trashedTopics->count() != 0)
                    @foreach ($trashedTopics as $topic)
                        @include('nexus.restore.topic', $topic)
                    @endforeach
                @else
                    <div class="alert alert-info my-3">
                        You don't have any topics to restore.
                    </div>
                @endif
            </div>

        @endif
    </div>

@endsection
