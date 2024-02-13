<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TravelRequest;
use App\Http\Resources\TravelResource;
use App\Models\Travel;
use Illuminate\Http\Request;

class TravelController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(TravelRequest $request)
    {
        return new TravelResource(Travel::create($request->validated()));
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(TravelRequest $request, string $slug)
    {
        $travel = Travel::where('slug',$slug)->first();
        $travel->update($request->validated());
        return new TravelResource($travel);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
