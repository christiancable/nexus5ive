@extends('layouts.master')

@section('meta')
    <title>Settings</title>
@endsection

@section('breadcrumbs')
    @include('_breadcrumbs', $breadcrumbs)
@endsection

@section('content')
    <div class="container">
        @include('shared._heading',[
        $heading = 'Settings',
        $lead = 'Change settings for the entire BBS'])
    </div>

    <div class="container">
        <div class="content">
            <p>Nothing much to see here yet...</p>
        </div>
    </div>
    <div class="container">
        <div class="content">
            <form action="{{ route('mode.activate') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label for="exampleFormControlSelect1">BBS Mode is
                        <strong>{{ $currentMode->name }}</strong>.{{ $currentMode->override ? ' This overrides any user selected theme' : '' }}</label>
                    <select name="mode" class="custom-select" id="mode">
                        @foreach ($modes as $mode)
                            <option value="{{ $mode->id }}" {{ $mode->active ? 'selected' : '' }}>
                                {{ $mode->name }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary btn-lg btn-block" type="submit">Set Mode</button>
            </form>
        </div>
    </div>

@endsection
