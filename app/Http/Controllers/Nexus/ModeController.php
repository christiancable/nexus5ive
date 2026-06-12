<?php

namespace App\Http\Controllers\Nexus;

use App\Helpers\ActivityHelper;
use App\Helpers\BreadcrumbHelper;
use App\Http\Controllers\Controller;
use App\Models\Mode;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ModeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): View
    {
        ActivityHelper::updateActivity(
            $request->user()->id,
            'Updating Default Theme',
        );
        $breadcrumbs = BreadcrumbHelper::breadcumbForUtility('Default Theme');

        return view(
            'nexus.admin.modes.index',
            [
                'breadcrumbs' => $breadcrumbs,
            ],
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): void
    {
        //
    }

    public function store(Request $request): void
    {
        //
    }

    public function show(Mode $mode): void
    {
        //
    }

    public function edit(Mode $mode): void
    {
        //
    }

    public function update(Request $request, Mode $mode): void
    {
        //
    }

    public function destroy(Mode $mode): void
    {
        //
    }
}
