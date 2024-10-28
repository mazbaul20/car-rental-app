<?php

namespace App\Http\Controllers\Admin;

use App\Models\Car;
use App\Models\Rental;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $totalCars = Car::count();
        $availableCars = Car::where('availability', true)->count();
        $totalRentals = Rental::count();
        $totalEarnings = Rental::where('status', 'Completed')->sum('total_cost');

        $data = [
            'totalCars' => $totalCars,
            'availableCars' => $availableCars,
            'totalRentals' => $totalRentals,
            'totalEarnings' => $totalEarnings,
        ];

        return view('admin.admin_dashboard', $data);
    }
    public function Logout(Request $request){
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
