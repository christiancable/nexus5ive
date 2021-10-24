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
            Current BBS Mode is
            <strong>{{ $currentMode->name }}</strong>.{{ $currentMode->override ? ' This overrides any user selected theme' : '' }}
            <hr />
        </div>
    </div>

    <div class="container">
        <div class="content">
            <form action="{{ route('mode.handle') }}" method="POST">
                @csrf

                <div id="app" v-cloak>
                    <mode-edit :modes="{{ json_encode($modes) }}" :current_mode_id="{{ $currentMode->id }}"
                        :themes="{{ json_encode($themes) }}">
                    </mode-edit>
                </div>


                <div class="form-group">
                    <button id="update" class="btn btn-success" value="update" name="action" type="submit">Update
                        mode</button>
                    <button id="activate " class="btn btn-primary" value="activate" name="action" type="submit">Set BBS
                        mode</button>
                </div>
            </form>
        </div>
    </div>

@endsection
