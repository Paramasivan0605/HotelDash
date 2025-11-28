<?php

namespace App\Http\Controllers\Admin\StaffAccount;

use App\Http\Controllers\Controller;
use App\Models\StaffAccount;
use App\Models\User;
use App\Models\Location;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class StaffAccountController extends Controller
{

    public function index() : View
    {
        $staff = User::where('role', 2)->with('location')->orderBy('created_at','DESC')->paginate(10);
        return view('company.admin.staff-account.index', ['staff' => $staff]);
    }


    /*
    *  Function to search resource in index page
    */
    public function search_index(Request $request) : View
    {
        $keyword = $request->input('search');

        $search = User::where('role', 2)
        ->where(function ($query) use ($keyword) {
            $query->where('staff_id', 'like', '%' . $keyword . '%')
                ->orWhere('name', 'like', '%' . $keyword . '%')
                ->orWhere('email', 'like', '%' . $keyword . '%')
                ->orWhere('phone', 'like', '%' . $keyword . '%');
        })->paginate(10);

        Log::info([$keyword, $search]);

        return view('company.admin.staff-account.index', ['staff' => $search]);
    }




    /*
    * To view create file
    */
    public function create() : View
    {
        $staffid = StaffAccount::Orderby('created_at','DESC')->with('location')->paginate(5);
        $location = Location::get();

        return view('company.admin.staff-account.create', ['staffid' => $staffid,'locations' => $location]);
    }




    /*
    *  Function to search resource in create page
    */
    public function search_create(Request $request) : View
    {
        $keyword = $request->input('search');

        $search = StaffAccount::where(function ($query) use ($keyword) {
            $query->where('staff_account_id', 'like', '%' . $keyword . '%')
            ->orWhere('created_at', 'like', '%' . $keyword . '%')
            ->orWhere('updated_at', 'like', '%' . $keyword . '%');
        })->paginate(5);

        Log::info([$keyword, $search]);

        return view('company.admin.staff-account.create', ['staffid' => $search]);
    }




    /*
    * Function to add new staff ID
    */
public function store(Request $request) : RedirectResponse
{
    $validator = Validator::make($request->all(), [
        'staff_id'     => 'required|min:10|max:12|unique:staff_accounts,staff_account_id|unique:users,staff_id',
        'location_id'  => 'required|exists:location,location_id',
        'name'         => 'required|string|max:255',
        'email'        => 'required|email|unique:users,email',
        'phone'        => 'required|numeric|digits_between:10,15',
        'gender'       => 'required|in:Male,Female',
        'password'     => 'required|confirmed|min:6',
        'position'     => 'nullable|string|max:100',
        'address'      => 'nullable|string|max:500',
    ], [
        'location_id.required' => 'Please select a location.',
        'staff_id.unique'      => 'This Staff ID is already taken.',
        'email.unique'         => 'This email is already registered.',
    ]);

    if ($validator->fails()) {
        return back()->withErrors($validator)->withInput();
    }

    // Create StaffAccount entry
    $staffAccount = StaffAccount::create([
        'staff_account_id' => $request->staff_id,
        'location_id'      => $request->location_id,
    ]);

    // Create User account (role = 2 for staff)
    $user = User::create([
        'name'         => $request->name,
        'email'        => $request->email,
        'phone'        => $request->phone,
        'gender'       => $request->gender,
        'staff_id'     => $request->staff_id,
        'location_id'  => $request->location_id,
        'position'      => $request->position,
        'address'      => $request->address,
        'password'     => Hash::make($request->password),
        'role'         => 2, // Staff role
    ]);

    Log::info('New staff account created by admin', ['staff_id' => $request->staff_id, 'user_id' => $user->id]);

    return back()->with('success-message', "Staff ID & Login Created Successfully!<br><strong>ID:</strong> {$request->staff_id}<br><strong>Email:</strong> {$request->email}");
}




    /*
    * Function view show file
    */
    public function show($id) : View
    {
        $user = User::findOrFail($id);

        return view('company.admin.staff-account.show', ['user' => $user]);
    }




    /*
    * Function view edit file
    */
public function edit($id)
{
    $user = User::findOrFail($id);
    $locations = Location::all(); // fetches all locations

    return view('company.admin.staff-account.edit', [
        'user' => $user,
        'locations' => $locations
    ]);
}





    /*
    *  Function to update staff account data
    */
    public function update(Request $request, $id) : RedirectResponse
    {
        $user = User::findOrFail($id);

        if ($request->anyFilled(['name', 'email', 'phone', 'position', 'address'])) {

            $updateData = [];

            if ($request->filled('name')) {
                $updateData['name'] = $request->input('name');
            }

            if ($request->filled('email')) {
                $updateData['email'] = $request->input('email');
            }

            if ($request->filled('phone')) {
                $updateData['phone'] = $request->input('phone');
            }

            if ($request->filled('position')) {
                $updateData['position'] = $request->input('position');
            }

            if ($request->filled('address')) {
                $updateData['address'] = $request->input('address');
            }

            $updated = $user->update($updateData);

            Log::info([$updateData, $updated]);

            return redirect()->route('staff-account-show', $user->id)->with('success-message', 'Your changes are saved successfully.');
        }
        else {
            return back()->withErrors([
                'error-message' => 'Please insert data to update staff details.'
            ]);
        }
    }




    /*
    *  Function to delete staff data
    */
/**
 * Delete a staff account ID record
 */
public function destroyStaffAccountId($staff_account_id) : RedirectResponse
{
    $staffAccount = StaffAccount::where('staff_account_id', $staff_account_id)->firstOrFail();
    
    // Optional: Also delete the associated User
    $user = User::where('staff_id', $staff_account_id)->first();
    if ($user) {
        $user->delete();
    }
    
    $staffAccount->delete();
    
    Log::info('Staff Account ID deleted', ['staff_account_id' => $staff_account_id]);
    
    return back()->with('success-message', 'Staff ID deleted successfully.');
}
}
