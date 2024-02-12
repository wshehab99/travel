<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\TourResource;
use App\Http\Requests\ToruListRequest;
use App\Models\Travel;
use Illuminate\Http\Request;

class TourController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index($slug, ToruListRequest $request)
    {
        $tours = Travel::where('slug',$slug)
            ->first()
            ->tours()
            ->when($request->date_from, function($query) use ($request){
                $query->where('starting_date', '>=', $request->date_from);
            })
            ->when($request->date_to, function($query) use ($request){
                $query->where('starting_date', '<=', $request->date_to);
            })
            ->when($request->price_from, function($query) use ($request){
                $query->where('price', '>=', $request->price_from*100);
            })
            ->when($request->price_to, function($query) use ($request){
                $query->where('price', '<=', $request->price_to*100);
            })
            ->when($request->sort_by && $request->sort_order, function($query) use ($request){
                $query->orderBy($request->sort_by,  $request->sort_order);
            })
            
            ->orderBy('starting_date')
            ->paginate(config('app.pagination.tours'));
        return TourResource::collection($tours);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
