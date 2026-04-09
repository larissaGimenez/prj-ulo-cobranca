<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Http\Requests\StoreSupportRequest;
use App\Http\Requests\UpdateSupportRequest;
use App\Models\Support;

class SupportController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $supports = Support::latest()->paginate(10);
        return view('supports.index', compact('supports'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSupportRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Support $support)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Support $support)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSupportRequest $request, Support $support)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Support $support)
    {
        //
    }
}
