<?php

namespace App\Http\Controllers\Admin;

use App\Models\Car;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;

class CarController extends Controller
{
    public function index()
    {
        $cars = Car::latest()->paginate(5);
        return view('admin.cars.index', compact('cars'));
    }

    // Show form to create a new car
    public function create()
    {
        return view('admin.cars.create');
    }

    // Store new car in the database
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'brand' => 'required',
            'model' => 'required',
            'year' => 'required|integer',
            'car_type' => 'required',
            'daily_rent_price' => 'required|numeric',
            'availability' => 'required|boolean',
            'image' => 'required|image|max:2048',
        ]);

        // Store the uploaded image
        $file = $request->file('image');
        $t = time();
        $ext = $file->getClientOriginalExtension();
        $img_url = "car_images/$t.$ext";

        $file->move(public_path('car_images/'), "$t.$ext");

        Car::create([
            'name' => $request->name,
            'brand' => $request->brand,
            'model' => $request->model,
            'year' => $request->year,
            'car_type' => $request->car_type,
            'daily_rent_price' => $request->daily_rent_price,
            'availability' => $request->availability,
            'image' => $img_url,
        ]);
        Toastr::success('Car added successfully');
        return redirect()->route('cars.index')->with('success', 'Car added successfully.');
    }

    // Show form to edit car details
    public function edit($id)
    {
        $car = Car::findOrFail($id);
        return view('admin.cars.edit', compact('car'));
    }
    public function show($id)
    {
        $car = Car::findOrFail($id);
        return view('admin.cars.show', compact('car'));
    }
    // Update car details
    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'brand' => 'required',
            'model' => 'required',
            'year' => 'required|integer',
            'car_type' => 'required',
            'daily_rent_price' => 'required|numeric',
            'availability' => 'required|boolean',
            'image' => 'nullable|image|max:2048'
        ]);

        $car = Car::findOrFail($id);

        if ($request->hasFile('image')) {
            if (file_exists($car->image)) {
                unlink($car->image);
            }

            $file = $request->file('image');
            $t = time();
            $ext = $file->getClientOriginalExtension();
            $img_url = "car_images/$t.$ext";

            $file->move(public_path('car_images/'), "$t.$ext");

            $car->update([
                'name' => $request->name,
                'brand' => $request->brand,
                'model' => $request->model,
                'year' => $request->year,
                'car_type' => $request->car_type,
                'daily_rent_price' => $request->daily_rent_price,
                'availability' => $request->availability,
                'image' => $img_url,
            ]);
        } else {
            $car->update([
                'name' => $request->name,
                'brand' => $request->brand,
                'model' => $request->model,
                'year' => $request->year,
                'car_type' => $request->car_type,
                'daily_rent_price' => $request->daily_rent_price,
                'availability' => $request->availability,
            ]);
        }

        Toastr::success('Car updated successfully');
        return redirect()->route('cars.index')->with('success', 'Car updated successfully.');
    }


    // Delete a car
    public function destroy(Car $car)
    {
        unlink($car->image);
        $car->delete();
        Toastr::success('Car deleted successfully');
        return redirect()->back()->with('success', 'Car deleted successfully.');
    }
}
