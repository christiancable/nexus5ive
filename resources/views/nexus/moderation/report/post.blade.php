@extends('nexus.layouts.master')

@section('meta')
    <title>Report post</title>
@endsection

@section('content')
    <div class="container mt-4">

        <x-heading heading="Report post" lead="Help make '{{ config('nexus.name') }}' a better place">
            <x-slot:icon>
                <x-heroicon-s-flag class="icon_large mr-1" aria-hidden="true" />
            </x-slot>
        </x-heading>

        <h2>Post preview</h2>
        <!-- Show the post being reported -->
        <x-post :post=$post :preview=true :userCanSeeSecrets="$userCanSeeSecrets"></x-post>

        <!-- Report form -->
        <form action="{{ action('App\Http\Controllers\Nexus\ReportController@store', ['type' => 'post', 'id' => $post->id]) }}" method="POST">
            @csrf

            <div class="mb-3">
                <label for="reason" class="form-label">Reason for report:</label>
                <select name="reason" id="reason" class="form-select" required>
                    <option value="">Select a reason</option>
                    @foreach (\App\Models\Report::REASONS as $key => $label)
                        <option value="{{ $key }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <div class="mb-3">
                <label for="details" class="form-label">Optional details:</label>
                <textarea name="details" id="details" class="form-control" rows="3"
                    placeholder="Provide any additional details or context (optional)"></textarea>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" name="anonymous" value="1" class="form-check-input" id="anonymous">
                <label class="form-check-label" for="anonymous">Report anonymously</label>
            </div>

            <button type="submit" class="btn btn-primary">Submit Report</button>
            <a href="{{ url()->previous() }}" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
@endsection
