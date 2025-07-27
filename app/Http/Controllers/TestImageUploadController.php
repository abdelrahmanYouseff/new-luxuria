<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestImageUploadController extends Controller
{
    /**
     * Show the test image upload page
     */
    public function index()
    {
        $vehicles = Vehicle::orderBy('make')->orderBy('model')->get();
        $vehiclesWithImages = Vehicle::whereNotNull('image')->get();

        return view('test_image_upload', compact('vehicles', 'vehiclesWithImages'));
    }

    /**
     * Handle image upload for a vehicle
     */
    public function upload(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            $vehicle = Vehicle::findOrFail($request->vehicle_id);

            // Delete old image if exists
            if ($vehicle->image && !filter_var($vehicle->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($vehicle->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('vehicles', 'public');

            // Update vehicle with new image path
            $vehicle->update(['image' => $imagePath]);

            return redirect()->back()->with([
                'success' => "Image uploaded successfully for {$vehicle->make} {$vehicle->model}!",
                'image_path' => $imagePath
            ]);

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to upload image: ' . $e->getMessage());
        }
    }

    /**
     * Remove image from a vehicle
     */
    public function removeImage($vehicleId)
    {
        try {
            $vehicle = Vehicle::findOrFail($vehicleId);

            if ($vehicle->image && !filter_var($vehicle->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($vehicle->image);
            }

            $vehicle->update(['image' => null]);

            return redirect()->back()->with('success', "Image removed from {$vehicle->make} {$vehicle->model}!");

        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Failed to remove image: ' . $e->getMessage());
        }
    }

    /**
     * Test image URL generation
     */
    public function testImageUrls()
    {
        $vehicles = Vehicle::all();
        $results = [];

        foreach ($vehicles as $vehicle) {
            $results[] = [
                'id' => $vehicle->id,
                'make' => $vehicle->make,
                'model' => $vehicle->model,
                'image_field' => $vehicle->image,
                'image_url' => $vehicle->image_url,
                'has_image' => !empty($vehicle->image),
            ];
        }

        return response()->json($results);
    }
}
