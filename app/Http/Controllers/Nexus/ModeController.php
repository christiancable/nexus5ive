<?php

namespace App\Http\Controllers\Nexus;

use App\Mode;
use App\Theme;
use Illuminate\Http\Request;
use App\Helpers\FlashHelper;
use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
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
        $this->middleware('auth');
        $this->middleware('verified');
        $this->authorizeResource(Mode::class, 'mode');
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
            "Settings",
        );
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Settings');

        return view(
            'modes.index',
            [
                'currentMode' => Mode::where('active', 1)->first() ?? Mode::first(),
                'modes' => Mode::all()->keyBy('id'),
                'themes' => Theme::all()->keyBy('id'),
                'breadcrumbs' => $breadcrumbs
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
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Mode $mode
     * @return \Illuminate\Http\Response
     */
    public function show(Mode $mode)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Mode $mode
     * @return \Illuminate\Http\Response
     */
    public function edit(Mode $mode)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \App\Mode                $mode
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Mode $mode)
    {
        //
    }

    /**
     * Set the specified mode as the default.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function activate(Request $request)
    {
        xdebug_break();

        /*
            mode: 1
            welcome: "some text"
            theme_id: 3
            theme_override: true

        */
        $validator = Validator::make(
            $request->all(),
            [
            'mode' => 'required',
            'mode' => 'exists:App\Mode,id',
            ]
        );

        if ($validator->fails()) {
            FlashHelper::showAlert('Unable to update mode', 'warning');
            return back();
        }

          // Retrieve the validated input...
          $validated = $validator->validated();
          $mode = Mode::findOrFail($validated['mode']);

        // unset any modes set to default
        Mode::where('active', true)->update(['active' => false]);

        // forget any existing cache @see App\Providers\AppServiceProvider
        Cache::forget('bbs_mode');

        // set that chosen mode as the new default
        $mode->active = true;
        $mode->save();


        $message = <<< Markdown
        Setting Mode to **{$mode->name}**
        Markdown;
        FlashHelper::showAlert($message, 'success');
        return back();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Mode $mode
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mode $mode)
    {
        //
    }
}
