<?php

namespace App\Http\Controllers\Admin\FoodMenu;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\FoodLocation;
use App\Models\FoodCategory;
use App\Models\FoodMenu;
use App\Models\Location;
use Illuminate\View\View;

class FoodLocationController extends Controller
{

    public function index() : View
    {
        $food = FoodLocation::paginate(10);

        return view('company.admin.food-location.index', ['food' => $food]);
    }

    /*
    *  Funtion to view create file
    */
    public function create() : View
    {
        $foodMenu = FoodMenu::get();
        $locations = Location::get();

        return view('company.admin.food-location.create', ['foodMenu' => $foodMenu, 'locations' => $locations]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        dd($request->all());
        $request->validate([
            'food_menu_id' => 'required|exists:food_menus,id',
            'location_id' => 'required|exists:locations,id',
        ]);

        $foodLocation = new FoodLocation();
        $foodLocation->food_menu_id = $request->input('food_menu_id');
        $foodLocation->location_id = $request->input('location_id');
        $foodLocation->save();

        return redirect()->route('food-location')->with('success', 'Food Location created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        
    }

    public function search_index(Request $request)
    {
        $search = $request->input('search');
        $foodLocations = FoodLocation::with(['foodMenu', 'location'])
            ->whereHas('foodMenu', function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->orWhereHas('location', function($query) use ($search) {
                $query->where('name', 'like', '%' . $search . '%');
            })
            ->paginate(10);

        return view('company.admin.food-location.index', ['foodLocations' => $foodLocations]);
    }

    public function search_create(Request $request)
    {
        $search = $request->input('search');
        $FoodMenu = FoodMenu::where('name', 'like', '%' . $search . '%')->get();
        $Location = Location::where('location_name', 'like', '%' . $search . '%')->get();

        return view('company.admin.food-location.create', ['FoodMenu' => $FoodMenu, 'Location' => $Location]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
