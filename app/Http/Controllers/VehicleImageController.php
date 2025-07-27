<?php

namespace App\Http\Controllers;

use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

class VehicleImageController extends Controller
{
    /**
     * Show the image management page for a specific vehicle
     */
    public function show(Vehicle $vehicle)
    {
        return Inertia::render('VehicleImage', [
            'vehicle' => [
                'id' => $vehicle->id,
                'make' => $vehicle->make,
                'model' => $vehicle->model,
                'plate_number' => $vehicle->plate_number,
                'current_image' => $vehicle->image_url,
                'has_image' => !empty($vehicle->image),
                'image_path' => $vehicle->image
            ]
        ]);
    }

    /**
     * Upload image for a specific vehicle
     */
    public function upload(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        ]);

        try {
            // Delete old image if exists
            if ($vehicle->image && !filter_var($vehicle->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($vehicle->image);
            }

            // Store new image
            $imagePath = $request->file('image')->store('vehicles', 'public');

            // Update vehicle with new image path
            $vehicle->update(['image' => $imagePath]);

            return response()->json([
                'success' => true,
                'message' => "Image uploaded successfully for {$vehicle->make} {$vehicle->model}!",
                'image_path' => $imagePath,
                'image_url' => $vehicle->fresh()->image_url
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to upload image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Remove image from a specific vehicle
     */
    public function remove(Vehicle $vehicle)
    {
        try {
            if ($vehicle->image && !filter_var($vehicle->image, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete($vehicle->image);
            }

            $vehicle->update(['image' => null]);

            return response()->json([
                'success' => true,
                'message' => "Image removed from {$vehicle->make} {$vehicle->model}!"
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to remove image: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Show image management page with Blade (fallback)
     */
    public function showBlade(Vehicle $vehicle)
    {
        return view('vehicle_image_management', compact('vehicle'));
    }
}
