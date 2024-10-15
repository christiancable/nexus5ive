<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Http\Controllers\Controller;
use App\Models\Mode;
use Illuminate\Http\Request;

class ModeController extends Controller
{
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
            'nexus.modes.index',
            [
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
     * Remove the specified resource from storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Mode $mode)
    {
        //
    }
}
