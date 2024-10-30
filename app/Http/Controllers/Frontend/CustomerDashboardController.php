<?php

namespace App\Http\Controllers\Frontend;

use App\Models\Rental;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class CustomerDashboardController extends Controller
{

    public function RentalHistory(){
        $rentals = Rental::where('user_id', Auth::id())->where('status','!=','Pending')->with('car')->paginate(6);
        // return $rentals;
        return view('frontend.customer_dashboard.rentals.rental-history', compact('rentals'));
    }//end method

    public function Logout(Request $request){
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/');
    }//end method


}
