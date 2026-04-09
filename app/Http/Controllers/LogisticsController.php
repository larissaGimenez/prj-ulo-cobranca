<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Http\Requests\StoreLogisticsRequest;
use App\Http\Requests\UpdateLogisticsRequest;
use App\Models\Logistics;

class LogisticsController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $logistics = Logistics::latest()->paginate(10);
        return view('logistics.index', compact('logistics'));
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
    public function store(StoreLogisticsRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Logistics $logistics)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Logistics $logistics)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLogisticsRequest $request, Logistics $logistics)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Logistics $logistics)
    {
        //
    }
}
