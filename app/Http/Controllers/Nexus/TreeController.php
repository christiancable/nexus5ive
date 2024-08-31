<?php

namespace App\Http\Controllers\Nexus;

use App\Http\Controllers\Controller;
use App\Tree;
use Illuminate\Support\Facades\Cache;

class TreeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('verified');
    }

    /**
     * Display the specified resource.
     *
     * @return array
     */
    public function show(Tree $tree)
    {
        return Cache::rememberForever('tree', function () {
            return Tree::tree();
        });
    }
}
