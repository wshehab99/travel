<?php

namespace App\Http\Controllers\Api\V1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\TourRequest;
use App\Http\Resources\TourResource;
use App\Models\Travel;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function store(TourRequest $request, string $slug)
    {
        $travel = Travel::where('slug', $slug)->first();
        return new TourResource($travel->tours()->create($request->validated()));
    }
}
