<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Car;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PageController extends Controller
{
    // Show the homepage
    public function home()
    {
        $cars = Car::where('availability', 1)->latest()->get();
        return view('frontend.pages.home', compact('cars'));
    }

    // Show the About page
    public function about()
    {
        return view('frontend.pages.about');
    }

    // Show the Contact page
    public function contact()
    {
        return view('frontend.pages.contact');
    }

    // Show the Rentals page
    public function rentals(Request $request)
    {
        // dd($request->all());
        $cars = Car::query()
        ->where('availability', 1)
        ->when($request->car_type, function ($query, $carType) {
            return $query->where('car_type', $carType);
        })
        ->when($request->brand, function ($query, $brand) {
            return $query->where('brand', $brand);
        })
        ->when($request->daily_rent, function ($query, $dailyRent) {
            return $query->where('daily_rent_price', '<=', $dailyRent); // Less than or equal to daily rent
        })
        ->latest()
        ->get();

        $carTypes = Car::pluck('car_type')->unique()->toArray();
        $brands = Car::pluck('brand')->unique()->toArray();

        return view('frontend.pages.rentals', compact('cars', 'carTypes', 'brands'));
    }

    public function details($id)
    {
        $car = Car::find($id);
        return view('frontend.pages.carDetails', compact('car'));
    }
}
