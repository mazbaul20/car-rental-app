<?php

namespace App\Http\Controllers\Admin;

use App\Models\Car;
use App\Models\User;
use App\Models\Rental;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;

class RentalController extends Controller
{
    public function index()
    {
        $rentals = Rental::with('user','car')->latest()->paginate(5);
        return view('admin.rentals.index', compact('rentals'));
    }//end method

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $customers = User::where('role', 'customer')->get();
        $cars = Car::where('availability', 1)->get();

        return view('admin.rentals.create', compact('customers', 'cars'));
    }//end method

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'user_id' => 'required',
            'car_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required',
        ]);

        $userId = $request->input('user_id');
        $carId = $request->input('car_id');

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');


        //check if car is available for selected date range
        if (!$this->isCarAvailable($carId, $startDate, $endDate)) {
            Toastr::error('Car is not available for the selected dates');
            return redirect()->back()->withInput()->with('error', 'Car is not available for selected date range.');
        }

        //get days between two dates
        $days = ((strtotime($endDate) - strtotime($startDate)) / 86400) + 1;
        $car = Car::find($carId);
        $total_price = $days * $car->daily_rent_price;

        Rental::create([
            'user_id'=>$userId,
            'car_id'=>$carId,
            'start_date'=>$startDate,
            'end_date'=>$endDate,
            'total_cost'=>$total_price,
            'status'=>$request->input('status'),
        ]);

        Toastr::success('Rental created successfully');
        return redirect()->route('rentals.index');
    }//end method

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $rental = Rental::with('user','car')->where('id',$id)->first();
        return $rental;
    }//end method

    /**
     * Show the form for editing the specified resource.
     */
    public function edit($id)
    {
        $customers = User::where('role', 'customer')->get();
        $cars = Car::where('availability', 1)->get();
        $rental = Rental::with('user','car')->where('id',$id)->first();
        return view('admin.rentals.edit', compact('customers', 'cars','rental'));
    }//end method

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request,$id)
    {
        $request->validate([
            'user_id' => 'required',
            'car_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'status' => 'required',
        ]);

        $userId = $request->input('user_id');
        $carId = $request->input('car_id');

        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        //check if car is available for selected date range
        if (!$this->isCarAvailable($carId, $startDate, $endDate,$id)) {
            Toastr::error('Car is not available for the selected dates');
            return redirect()->back()->withInput()->with('error', 'Car is not available for selected date range.');
        }

        //get days between two dates
        $days = (strtotime($endDate) - strtotime($startDate)) / 86400 == 0 ? 1 : (strtotime($endDate) - strtotime($startDate)) / 86400;
        $car = Car::find($carId);
        $total_price = $days * $car->daily_rent_price;

        $rental = Rental::findOrFail($id);

        $rental->update([
            'user_id' => $userId,
            'car_id' => $carId,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'total_cost' => $total_price,
            'status' => $request->input('status'),
        ]);
        Toastr::success('Rental updated successfully');
        return redirect()->route('rentals.index')->with('success', 'Rental updated successfully');
    }//end method

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Rental $rental)
    {
        if ($rental->status == 'Ongoing') {
            Toastr::error('Ongoing rentals cannot be deleted.');
            return redirect()->back()->with('error', 'Ongoing rentals cannot be deleted.');
        }
        $rental->delete();
        Toastr::success('Rental deleted successfully');
        return redirect()->route('rentals.index')->with('success', 'Rental deleted successfully');
    }//end method

    private function isCarAvailable($carId, $startDate, $endDate, $rentalId = null)
    {
        $dateOverlap = Rental::where('car_id', $carId)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->whereBetween('start_date', [$startDate, $endDate])
                    ->orWhereBetween('end_date', [$startDate, $endDate])
                    ->orWhere(function ($query) use ($startDate, $endDate) {
                        $query->where('start_date', '<=', $startDate)
                            ->where('end_date', '>=', $endDate);
                    });
            })
            ->when($rentalId, function ($query) use ($rentalId) {
                $query->where('id', '!=', $rentalId); // বর্তমান রেন্টাল বাদ দিন
            })
            ->exists();

        return !$dateOverlap;
    }//end method
}
