<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Car;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class CarController extends Controller
{
    public function index(Request $request)
    {
        $cars = Car::query();

        // Apply filters if provided
        if ($request->has('car_type')) {
            $cars->where('car_type', $request->car_type);
        }
        if ($request->has('brand')) {
            $cars->where('brand', $request->brand);
        }
        if ($request->has('min_price')) {
            $cars->where('daily_rent_price', '>=', $request->min_price);
        }
        if ($request->has('max_price')) {
            $cars->where('daily_rent_price', '<=', $request->max_price);
        }

        $cars = $cars->where('availability', true)->get();

        return view('frontend.cars.index', compact('cars'));
    }

    // Show details of a specific car
    public function show($id)
    {
        $car = Car::findOrFail($id);
        return view('frontend.cars.show', compact('car'));
    }
}
