<?php

namespace App\Http\Controllers\Admin;

use App\Models\Car;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Brian2694\Toastr\Facades\Toastr;

class CustomerController extends Controller
{
    public function index()
    {
        $customers = User::where('role', 'customer')->latest()->paginate(5);
        return view('admin.customers.index', compact('customers'));
    }//end method

    // Show form to create a new customer (optional for admin)
    public function create()
    {
        return view('admin.customers.create');
    }//end method

    // Store new customer in the database (optional for admin)
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email',
            // 'password' => 'required|min:6',
            'phone' => 'nullable',
            'address' => 'nullable',
        ]);

        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => bcrypt($request->password),
            'role' => 'customer',
            'phone' => $request->phone,
            'address' => $request->address,
        ]);
        Toastr::success('Customers Craeted successfully');
        return redirect()->route('customers.index');
    }//end method

    // Show form to edit customer details
    public function edit(User $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }//end method

    public function show($id)
    {
        $customer = User::findOrFail($id);
        $rentals = $customer->rentals()->paginate(5);
        $car = Car::all();
        // return $customer;
        return view('admin.customers.show', compact('customer','rentals','car'));
    }//end method

    // Update customer details
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$id,
            'phone' => 'nullable',
            'address' => 'nullable',
        ]);
        $customer = User::findOrFail($id);

        $customer->update($request->all());
        Toastr::success('Customer updated successfully');
        return redirect()->route('customers.index');
    }//end method

    // Delete a customer
    public function destroy(User $customer)
    {
        if ($customer->rentals()->exists()) {
            Toastr::error('Customer cannot be deleted because they have rental history.');
            return redirect()->back()->with('error', 'Customer cannot be deleted because they have rental history.');
        }
        $customer->delete();
        Toastr::success('Customer deleted successfully');
        return redirect()->route('customers.index');
    }//end method
}
