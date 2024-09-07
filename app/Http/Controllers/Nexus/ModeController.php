<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Helpers\FlashHelper;
use App\Http\Controllers\Controller;
use App\Mode;
use App\Theme;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class ModeController extends Controller
{
    /**
     * __construct
     * allowed users are auth, verified, sysops
     *
     * @return void
     */
    public function __construct()
    {
        // $this->middleware('auth');
        // $this->middleware('verified');
        // $this->authorizeResource(Mode::class, 'mode');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        ActivityHelper::updateActivity(
            $request->user()->id,
            'Settings',
        );
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Settings');

        return view(
            'modes.index',
            [
                'currentMode' => Mode::where('active', 1)->first() ?? Mode::first(),
                'modes' => Mode::all()->keyBy('id'),
                'themes' => Theme::all()->keyBy('id'),
                'breadcrumbs' => $breadcrumbs,
            ],
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Mode $mode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function edit(Mode $mode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mode $mode)
    {
        //
    }

    /**
     * Handle form submission and pass input onto activate or update
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'mode' => [
                    'required',
                    'exists:App\Mode,id',
                ],
                'welcome' => 'required',
                'theme_id' => 'exists:App\Theme,id',
                'action' => [
                    'required',
                    'in:update,activate',
                ],
                'theme_override' => 'sometimes',
            ],
        );

        if ($validator->fails()) {
            FlashHelper::showAlert('Unable to update mode', 'warning');

            return back();
        }

        $validatedData = $validator->validated();
        $mode = Mode::findOrFail($validatedData['mode']);

        // forget any existing cache @see App\Providers\AppServiceProvider
        Cache::forget('bbs_mode');

        switch ($validatedData['action']) {
            case 'update':
                $message = $this->updateMode($mode, $validatedData);
                break;

            case 'activate':
                $message = $this->activate($mode);
                break;

            default:
                $message = [
                    'body' => 'nothing happened',
                    'status' => 'warning',
                ];
                break;
        }

        FlashHelper::showAlert($message['body'], $message['status']);

        return back();
    }

    /**
     * activate
     *
     * sets the Mode as the default
     */
    private function activate(Mode $mode): array
    {
        // unset any modes set to default
        Mode::where('active', true)->update(['active' => false]);
        // set that chosen mode as the new default
        $mode->active = true;
        $mode->save();

        $message = <<< Markdown
            Setting Mode to **{$mode->name}**
        Markdown;

        return [
            'body' => trim($message),
            'status' => 'success',
        ];
    }

    /**
     * updateMode
     *
     * saves changes to the Mode
     */
    private function updateMode(Mode $mode, array $updates): array
    {
        $mode->welcome = $updates['welcome'];
        $mode->theme_id = $updates['theme_id'];
        if (array_key_exists('theme_override', $updates)) {
            $mode->override = $updates['theme_override'] ? 1 : 0;
        } else {
            $mode->override = 0;
        }
        $mode->save();

        $message = <<< Markdown
            Updated **{$mode->name}**
        Markdown;

        return [
            'body' => trim($message),
            'status' => 'success',
        ];
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mode $mode)
    {
        //
    }
}
