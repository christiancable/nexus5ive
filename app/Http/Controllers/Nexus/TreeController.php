<?php

namespace App\Http\Controllers\Nexus;

use App\Tree;
use App\Section;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
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
    * @param  \App\Tree  $tree
    * @return Array
    */
    public function show(Tree $tree)
    {
        return Cache::rememberForever('tree', function () {
            return Tree::tree();
        });
    }
}
