<?php

namespace App\Http\Controllers\Frontend;

use Carbon\Carbon;
use App\Models\Car;
use App\Models\User;
use App\Models\Rental;
use App\Mail\AdminConfirmed;
use Illuminate\Http\Request;
use App\Mail\RentalConfirmed;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class RentalController extends Controller
{
    public function index()
    {
        $rentals = Rental::where('user_id', Auth::id())->where('status','Pending')->with('car')->latest()->paginate(6);
        // return $rentals;
        return view('frontend.customer_dashboard.rentals.index', compact('rentals'));
    }//end method

    // Show form to book a car
    public function create($carId)
    {
        // $car = Car::findOrFail($carId);
        // return view('frontend.rentals.create', compact('car'));
    }//end method

    public function store(Request $request){
        $request->validate([
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
        ]);

        date_default_timezone_set('Asia/Dhaka');
        $car_id = $request->input('car_id');
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        if (!Auth::check()) {
            // Store the previous URL in the session to redirect after login
            session()->put('previous_url', url()->previous());
            return redirect()->route('login');
        }

        //check if this car is available for selected date range
        if (!$this->isCarAvailable($car_id, $startDate, $endDate)) {
            Toastr::error('Car is not available for the selected dates');
            return redirect()->back()->with('error', 'Sorry, this car is not available for the selected date range.');
        }

        //get days between two dates
        $days = ((strtotime($endDate) - strtotime($startDate)) / 86400) + 1;
        $car = Car::find($car_id);
        $total_price = $days * $car->daily_rent_price;

        $rental = new Rental();
        $rental->user_id = Auth::user()->id;
        $rental->car_id = $car_id;
        $rental->start_date = $startDate;
        $rental->end_date = $endDate;
        $rental->total_cost = $total_price;
        $rental->status = 'Pending';
        $rental->save();

        //send email to user
        $userName = Auth::user()->name;
        $userAll = Auth::user();

        $userMessage = [
            'id'=>$userAll->id,
            'name' => $userName,
            'messageContent' => "You have booked a car for $days days from " . Carbon::parse($startDate)->format('d M, Y') . " to " . Carbon::parse($endDate)->format('d M, Y') . ".",
            'carName' => $car->name,
            'carBrand' => $car->brand,
            'carModel' => $car->model,
            'carType' => $car->car_type,
            'cost' =>  $total_price,
        ];

        $adminMessage = [
            'id'=>$userAll->id,
            'customerName'=>$userAll->name,
            'customerEmail'=>$userAll->email,
            'customerPhone'=>$userAll->phone,
            'name' => "Admin",
            'messageContent' => "$userName has booked a car for $days days from". Carbon::parse($startDate)->format('d M, Y') . " to " . Carbon::parse($endDate)->format('d M, Y') . ".",
            'carName' => $car->name,
            'carBrand' => $car->brand,
            'carModel' => $car->model,
            'carType' => $car->car_type,
            'cost' =>  $total_price,
        ];

        Mail::to(Auth::user()->email)->send(new RentalConfirmed($userMessage));
        Mail::to('mazbaul20@gmail.com')->send(new AdminConfirmed($adminMessage));

        Toastr::success('Rental created successfully');
        return redirect()->route('customer.rentals')->with('success', 'Car booked successfully.');
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

    // Cancel a booking (only if rental has not started yet)
    public function CancelBooking(Request $request,Rental $rental)
    {
        if ($rental->user_id === $request->user()->id) {
            //Cancel a booking (only if the rental has not started yet).
            if ($rental->status == "Pending" && $rental->start_date > now()->format('Y-m-d')) {
                $rental->status = "Canceled";
                $rental->save();

                Toastr::success('Rental canceled successfully');
                return redirect()->back()->with('success', 'Booking has been cancelled successfully.');
            }elseif($rental->status == "Canceled"){
                Toastr::error('Booking already cenceled');
                return redirect()->back();
            }
            Toastr::error('Sorry, this booking cannot be cancelled.');
            return redirect()->back()->with('error', 'Sorry, this booking cannot be cancelled.');
          } else {
            abort(403, 'You are not authorized to cancel this booking.');
          }
    }//end method
}
