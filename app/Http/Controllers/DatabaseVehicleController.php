<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use App\Services\VehicleApiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class DatabaseVehicleController extends Controller
{
    protected $vehicleApiService;

    public function __construct(VehicleApiService $vehicleApiService)
    {
        $this->vehicleApiService = $vehicleApiService;
    }

    /**
     * Display a listing of vehicles from database
     */
    public function index(Request $request)
    {
        $syncFromApi = $request->get('sync', false);

        if ($syncFromApi) {
            $syncResult = $this->vehicleApiService->syncVehicles();
        }

        $vehicles = Vehicle::orderBy('daily_rate', 'desc')->get();

        return Inertia::render('DatabaseVehicles', [
            'vehicles' => $vehicles,
            'syncResult' => $syncResult ?? null,
            'totalCount' => $vehicles->count()
        ]);
    }

    /**
     * Show the form for creating a new vehicle
     */
    public function create()
    {
        return Inertia::render('DatabaseVehicles/Create');
    }

    /**
     * Store a newly created vehicle
     */
    public function store(Request $request)
    {
        $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'nullable|string|max:4',
            'color' => 'nullable|string|max:100',
            'category' => 'required|string|in:economy,mid-range,luxury,suv,sports',
            'daily_rate' => 'required|numeric|min:0',
            'weekly_rate' => 'nullable|numeric|min:0',
            'monthly_rate' => 'nullable|numeric|min:0',
            'plate_number' => 'nullable|string|max:50',
            'status' => 'required|string|in:Available,Rented,Maintenance,Out of Service,Reserved',
            'ownership_status' => 'required|string|in:owned,leased,rented',
            'transmission' => 'required|string|in:Automatic,Manual',
            'seats' => 'required|integer|min:1|max:12',
            'doors' => 'required|integer|min:2|max:6',
            'odometer' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('vehicles', 'public');
            $data['image'] = $imagePath;
        }

        Vehicle::create($data);

        return redirect()->route('database.vehicles.index')->with('success', 'Vehicle created successfully!');
    }

    /**
     * Display the specified vehicle
     */
    public function show(Vehicle $vehicle)
    {
        return Inertia::render('DatabaseVehicles/Show', [
            'vehicle' => $vehicle
        ]);
    }

    /**
     * Show the form for editing the specified vehicle
     */
    public function edit(Vehicle $vehicle)
    {
        return Inertia::render('DatabaseVehicles/Edit', [
            'vehicle' => $vehicle
        ]);
    }

    /**
     * Update the specified vehicle
     */
    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'make' => 'required|string|max:255',
            'model' => 'required|string|max:255',
            'year' => 'nullable|string|max:4',
            'color' => 'nullable|string|max:100',
            'category' => 'required|string|in:economy,mid-range,luxury,suv,sports',
            'daily_rate' => 'required|numeric|min:0',
            'weekly_rate' => 'nullable|numeric|min:0',
            'monthly_rate' => 'nullable|numeric|min:0',
            'plate_number' => 'nullable|string|max:50',
            'status' => 'required|string|in:Available,Rented,Maintenance,Out of Service,Reserved',
            'ownership_status' => 'required|string|in:owned,leased,rented',
            'transmission' => 'required|string|in:Automatic,Manual',
            'seats' => 'required|integer|min:1|max:12',
            'doors' => 'required|integer|min:2|max:6',
            'odometer' => 'nullable|integer|min:0',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        $data = $request->all();

        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image
            if ($vehicle->image && !filter_var($vehicle->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($vehicle->image);
            }

            $imagePath = $request->file('image')->store('vehicles', 'public');
            $data['image'] = $imagePath;
        }

        $vehicle->update($data);

        return redirect()->route('database.vehicles.index')->with('success', 'Vehicle updated successfully!');
    }

    /**
     * Remove the specified vehicle
     */
    public function destroy(Vehicle $vehicle)
    {
        // Delete image
        if ($vehicle->image && !filter_var($vehicle->image, FILTER_VALIDATE_URL)) {
            Storage::disk('public')->delete($vehicle->image);
        }

        $vehicle->delete();

        return redirect()->route('database.vehicles.index')->with('success', 'Vehicle deleted successfully!');
    }

    /**
     * Sync vehicles from API
     */
    public function syncFromApi()
    {
        $result = $this->vehicleApiService->syncVehicles();

        if ($result['success']) {
            return redirect()->route('database.vehicles.index')->with('success',
                "Sync completed! {$result['synced_count']} new vehicles added, {$result['updated_count']} vehicles updated."
            );
        } else {
            return redirect()->route('database.vehicles.index')->with('error',
                'Sync failed: ' . ($result['error'] ?? 'Unknown error')
            );
        }
    }

    /**
     * Get vehicles by category
     */
    public function byCategory($category)
    {
        $vehicles = Vehicle::byCategory($category)->orderBy('daily_rate', 'desc')->get();

        return Inertia::render('DatabaseVehicles', [
            'vehicles' => $vehicles,
            'category' => $category,
            'totalCount' => $vehicles->count()
        ]);
    }

    /**
     * Get available vehicles
     */
    public function available()
    {
        $vehicles = Vehicle::available()->orderBy('daily_rate', 'desc')->get();

        return Inertia::render('DatabaseVehicles', [
            'vehicles' => $vehicles,
            'filter' => 'available',
            'totalCount' => $vehicles->count()
        ]);
    }
}
