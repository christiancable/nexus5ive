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
    * Display a listing of the resource.
    *
    * @return \Illuminate\Http\Response
    */
    public function index()
    {
        //
    }
    
    /**
    * ???
    *
    * @return \Illuminate\Http\Response
    */
    public function create()
    {
          
    }
    
    /**
    * Store a newly created resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @return \Illuminate\Http\Response
    */
    public function store(Request $request)
    {
        //
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
    
    /**
    * Show the form for editing the specified resource.
    *
    * @param  \App\Tree  $tree
    * @return \Illuminate\Http\Response
    */
    public function edit(Tree $tree)
    {
        //
    }
    
    /**
    * Update the specified resource in storage.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \App\Tree  $tree
    * @return \Illuminate\Http\Response
    */
    public function update(Request $request, Tree $tree)
    {
        //
    }
    
    /**
    * Remove the specified resource from storage.
    *
    * @param  \App\Tree  $tree
    * @return \Illuminate\Http\Response
    */
    public function destroy(Tree $tree)
    {
        //
    }
}
