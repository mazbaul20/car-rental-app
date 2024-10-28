<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Car;
use App\Models\Rental;
use Illuminate\Http\Request;
use App\Mail\RentalConfirmed;
use Illuminate\Support\Carbon;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RentalController extends Controller
{
    public function index()
    {
        $rentals = Rental::where('user_id', Auth::id())->with('car')->get();
        return view('frontend.rentals.index', compact('rentals'));
    }

    // Show form to book a car
    public function create($carId)
    {
        $car = Car::findOrFail($carId);
        return view('frontend.rentals.create', compact('car'));
    }

    // Store the booking details
    // public function stores(Request $request, $carId)
    // {
    //     $request->validate([
    //         'start_date' => 'required|date|after_or_equal:today',
    //         'end_date' => 'required|date|after_or_equal:start_date',
    //     ]);

    //     $car = Car::findOrFail($carId);

    //     // Check car availability for the selected dates
    //     $existingRental = Rental::where('car_id', $car->id)
    //         ->where(function ($query) use ($request) {
    //             $query->whereBetween('start_date', [$request->start_date, $request->end_date])
    //                   ->orWhereBetween('end_date', [$request->start_date, $request->end_date]);
    //         })
    //         ->first();

    //     if ($existingRental) {
    //         return back()->withErrors('The car is not available for the selected dates.');
    //     }

    //     // Calculate total cost
    //     $startDate = Carbon::parse($request->start_date);
    //     $endDate = Carbon::parse($request->end_date);
    //     $days = $endDate->diffInDays($startDate) + 1;
    //     $totalCost = $days * $car->daily_rent_price;

    //     // Store rental
    //     Rental::create([
    //         'user_id' => Auth::id(),
    //         'car_id' => $car->id,
    //         'start_date' => $request->start_date,
    //         'end_date' => $request->end_date,
    //         'total_cost' => $totalCost,
    //     ]);

    //     return redirect()->route('frontend.rentals.index')->with('success', 'Car booked successfully.');
    // }
    public function store(Car $car, Request $request){
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        date_default_timezone_set('Asia/Dhaka');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!Auth::check()) {
        // Store the previous URL in the session to redirect after login
        session()->put('previous_url', url()->previous());
        return redirect()->route('login');
        }

        //check if this car is available for selected date range
        if (!$this->isCarAvailable($car->id, $startDate, $endDate)) {
        return redirect()->back()->with('error', 'Sorry, this car is not available for the selected date range.');
        }

        //get days between two dates
        $days = ((strtotime($endDate) - strtotime($startDate)) / 86400) + 1;
        $total_price = $days * $car->daily_rent_price;

        $rental = new Rental();
        $rental->user_id = Auth::user()->id;
        $rental->car_id = $car->id;
        $rental->start_date = $startDate;
        $rental->end_date = $endDate;
        $rental->total_cost = $total_price;
        $rental->status = 'Pending';
        $rental->save();

        //send email to user
        $userName = Auth::user()->name;
        $userMessage = [
            'name' => $userName,
            'messageContent' => "You have booked a car for $days days from $startDate to $endDate.",
            'cost' =>  "$total_price.",
            'carBrand' => $car->brand,
            'carModel' => $car->model,
        ];
        $adminMessage = [
            'name' => "Admin",
            'messageContent' => "$userName has booked a car for $days days from $startDate to $endDate.",
            'cost' =>  "$total_price.",
            'carBrand' => $car->brand,
            'carModel' => $car->model,
        ];
        Mail::to(Auth::user()->email)->send(new RentalConfirmed($userMessage));
        Mail::to('admin@email.com')->send(new RentalConfirmed($adminMessage));

        return redirect()->back()->with('success', 'Car booked successfully.');
    }

    // Cancel a booking (only if rental has not started yet)
    public function cancel($id)
    {
        $rental = Rental::where('id', $id)->where('user_id', Auth::id())->firstOrFail();

        if (Carbon::now()->lt(Carbon::parse($rental->start_date))) {
            $rental->delete();
            return redirect()->route('frontend.rentals.index')->with('success', 'Booking canceled successfully.');
        }

        return back()->withErrors('You can only cancel bookings that haven’t started yet.');
    }
}
