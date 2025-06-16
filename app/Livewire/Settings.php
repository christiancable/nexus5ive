<?php

namespace App\Livewire;

use App\Helpers\FlashHelper;
use App\Models\Mode;
use App\Models\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Settings extends Component
{
    public $modes;

    public $themes;

    public $currentMode;

    public $selectedMode;

    #[Validate('required', message: 'Please select a theme for this mode.')]
    #[Validate('exists:App\Models\Theme,id', message: 'Please select a valid theme for this mode.')]
    public $selectedTheme;

    #[Validate('required', message: 'Please add a welcome message.')]
    public $welcome;

    #[Validate('required', message: 'Please fill out your date of birth.')]
    public $override;

    public function mount()
    {
        $this->modes = Mode::all()->keyBy('id');
        $this->themes = Theme::all()->keyBy('id');
        $this->currentMode = Mode::where('active', 1)->first() ?? Mode::first();
        $this->selectedMode = $this->currentMode->id;
        $this->changeCurrentMode();
    }

    /*
        update the form with the selected mode
    */
    public function changeCurrentMode(): void
    {
        // switch the currentMode to whatever has been selected and everything reacts?
        $this->currentMode = $this->modes[$this->selectedMode];
        $this->welcome = $this->currentMode->welcome;
        $this->override = $this->currentMode->override;
        $this->selectedTheme = $this->currentMode->theme_id;
    }

    public function save(Request $request)
    {
        // if user cannot save mode then bail
        if ($request->user()->cannot('update', Mode::class)) {
            abort(403);
        }

        $validated = $this->validate();
        $this->currentMode->welcome = $validated['welcome'];
        $this->currentMode->override = $validated['override'];
        $this->currentMode->theme_id = $validated['selectedTheme'];
        $this->currentMode->save();

        Cache::forget('bbs_mode');
        FlashHelper::showAlert('Saved changes', 'success');
        $this->redirect(route('theme.index'));
    }

    public function setBBSMode(Request $request)
    {
        // if user cannot save mode then bail
        if ($request->user()->cannot('update', Mode::class)) {
            abort(403);
        }

        // unset any modes set to default
        Mode::where('active', true)->update(['active' => false]);

        // set the chosen mode as the new default

        $this->currentMode->active = true;
        $this->currentMode->save();

        $message = "Setting BBS Mode to **{$this->currentMode->name}**";

        Cache::forget('bbs_mode');
        FlashHelper::showAlert($message, 'success');
        $this->redirect(route('theme.index'));
    }

    public function render()
    {
        return view('livewire.settings');
    }
}
