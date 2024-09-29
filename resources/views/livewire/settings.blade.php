<span>

    <div class="container">
        <div class="content">
            Current BBS Mode is
            <strong>{{ $currentMode->name }}</strong>.{{ $currentMode->override ? ' This overrides any user selected theme' : '' }}
            <hr />
        </div>
    </div>

    <div class="container">
        <div class="content">
            <span>
                <div class="form-group">
                    <label>Mode</label>
                    
                    <select name="mode" class="custom-select" id="mode" wire:model="selectedMode"  wire:change="changeCurrentMode">
                        @foreach($modes as $mode)
                            <option value="{{$mode->id}}">{{$mode->name}}</option>
                        @endforeach
                    </select>
                    <div class="text-danger">@error('selectedMode') {{ $message }} @enderror</div>
                </div>

                <div class="form-group">
                    <label for="welcome">Login Screen Welcome</label>
                    <textarea class="form-control" id="welcome" name="welcome" cols="40" rows="5"
                        wire:model="welcome"></textarea>
                    <div class="text-danger">@error('welcome') {{ $message }} @enderror</div>
                </div>

                {{-- make this match the mode --}}
                <div class="form-group">
                    <label>Mode theme</label>
                    <select name="mode" class="custom-select" id="theme_id" wire:model="selectedTheme">
                        @foreach($themes as $theme)
                            <option value="{{$theme->id}}">{{$theme->name}}</option>
                        @endforeach
                    </select>
                    <div class="text-danger">@error('selectedTheme') {{ $message }} @enderror</div>
                </div>

                <div class="form-group">
                    <label for="theme_override">All users use this theme</label>
                    <input name="theme_override" id="theme_override" type="checkbox" value="true"
                        wire:model="override" />
                </div>
            </span>
            <div class="form-group d-flex">
                    <x-button class="btn-success mr-2" type="submit" wire:click="save">Update mode</x-button>
                    <x-button class="btn-primary" wire:click="setBBSMode">Set BBS mode</x-button>
                </div>
        </div>
    </div>
</span>
