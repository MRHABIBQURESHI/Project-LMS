<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Traits\StoreFileTrait;

class PropertyController extends Controller
{
    use StoreFileTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $properties = Property::all();
        return view('panel.properties.index', compact('properties'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('panel.properties.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'property_type_id' => ['nullable', 'exists:property_types,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'developer_id' => ['nullable', 'exists:developers,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'size' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gallery_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'floor_plan_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'garages' => ['nullable', 'integer', 'min:0'],
            'air_conditioning' => ['nullable', 'boolean'],
            'alarm' => ['nullable', 'boolean'],
            'balcony' => ['nullable', 'boolean'],
            'cable_tv' => ['nullable', 'boolean'],
            'central_heating' => ['nullable', 'boolean'],
            'dryer' => ['nullable', 'boolean'],
            'dishwasher' => ['nullable', 'boolean'],
            'garage' => ['nullable', 'boolean'],
            'gym' => ['nullable', 'boolean'],
            'library' => ['nullable', 'boolean'],
            'laundry_room' => ['nullable', 'boolean'],
            'microwave' => ['nullable', 'boolean'],
            'oven' => ['nullable', 'boolean'],
            'parking' => ['nullable', 'boolean'],
            'pets_allowed' => ['nullable', 'boolean'],
            'refrigerator' => ['nullable', 'boolean'],
            'security_system' => ['nullable', 'boolean'],
            'swimming_pool' => ['nullable', 'boolean'],
            'tennis_court' => ['nullable', 'boolean'],
            'tv_cable' => ['nullable', 'boolean'],
            'wifi' => ['nullable', 'boolean'],
            'washer' => ['nullable', 'boolean'],
            'location_map' => ['nullable', 'string'], // could be JSON, but we treat as string
            'video_url' => ['nullable', 'url'],
        ]);

        DB::beginTransaction();

        try {
            $property = new Property();
            $property->project_id = $validated['project_id'] ?? null;
            $property->property_type_id = $validated['property_type_id'] ?? null;
            $property->city_id = $validated['city_id'] ?? null;
            $property->developer_id = $validated['developer_id'] ?? null;
            $property->name = $validated['name'];
            $property->slug = Str::slug($validated['name']);
            $property->description = $validated['description'] ?? null;
            $property->address = $validated['address'] ?? null;
            $property->price = $validated['price'] ?? null;
            $property->size = $validated['size'] ?? null;
            $property->is_featured = $validated['is_featured'] ?? false;
            $property->status = $validated['status'] ?? true;
            $property->bedrooms = $validated['bedrooms'] ?? null;
            $property->bathrooms = $validated['bathrooms'] ?? null;
            $property->garages = $validated['garages'] ?? 0;
            $property->location_map = $validated['location_map'] ?? null;
            $property->video_url = $validated['video_url'] ?? null;

            // Set all boolean amenities
            $booleans = [
                'air_conditioning',
                'alarm',
                'balcony',
                'cable_tv',
                'central_heating',
                'dryer',
                'dishwasher',
                'garage',
                'gym',
                'library',
                'laundry_room',
                'microwave',
                'oven',
                'parking',
                'pets_allowed',
                'refrigerator',
                'security_system',
                'swimming_pool',
                'tennis_court',
                'tv_cable',
                'wifi',
                'washer'
            ];
            foreach ($booleans as $field) {
                $property->$field = $validated[$field] ?? false;
            }

            // Store single image
            $property->image = $this->storeFile($request->file('image'), 'properties') ?? null;

            // Store gallery images (multiple)
            $galleryPaths = [];
            if ($request->hasFile('gallery_images')) {
                foreach ($request->file('gallery_images') as $file) {
                    $path = $this->storeFile($file, 'properties/gallery');
                    if ($path) {
                        $galleryPaths[] = $path;
                    }
                }
            }
            $property->gallery_images = !empty($galleryPaths) ? json_encode($galleryPaths) : null;

            // Store floor plan images (multiple)
            $floorPlanPaths = [];
            if ($request->hasFile('floor_plan_images')) {
                foreach ($request->file('floor_plan_images') as $file) {
                    $path = $this->storeFile($file, 'properties/floor_plans');
                    if ($path) {
                        $floorPlanPaths[] = $path;
                    }
                }
            }
            $property->floor_plan_images = !empty($floorPlanPaths) ? json_encode($floorPlanPaths) : null;

            $property->save();

            DB::commit();

            return redirect()->route('panel.properties.index')
                ->with('success', 'Property created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PropertyController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong while creating the property.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $property = Property::findOrFail($id);
        return view('panel.properties.edit', compact('property'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'project_id' => ['nullable', 'exists:projects,id'],
            'property_type_id' => ['nullable', 'exists:property_types,id'],
            'city_id' => ['nullable', 'exists:cities,id'],
            'developer_id' => ['nullable', 'exists:developers,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'address' => ['nullable', 'string', 'max:255'],
            'price' => ['nullable', 'numeric', 'min:0'],
            'size' => ['nullable', 'integer', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'gallery_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'floor_plan_images.*' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
            'bedrooms' => ['nullable', 'integer', 'min:0'],
            'bathrooms' => ['nullable', 'integer', 'min:0'],
            'garages' => ['nullable', 'integer', 'min:0'],
            'air_conditioning' => ['nullable', 'boolean'],
            'alarm' => ['nullable', 'boolean'],
            'balcony' => ['nullable', 'boolean'],
            'cable_tv' => ['nullable', 'boolean'],
            'central_heating' => ['nullable', 'boolean'],
            'dryer' => ['nullable', 'boolean'],
            'dishwasher' => ['nullable', 'boolean'],
            'garage' => ['nullable', 'boolean'],
            'gym' => ['nullable', 'boolean'],
            'library' => ['nullable', 'boolean'],
            'laundry_room' => ['nullable', 'boolean'],
            'microwave' => ['nullable', 'boolean'],
            'oven' => ['nullable', 'boolean'],
            'parking' => ['nullable', 'boolean'],
            'pets_allowed' => ['nullable', 'boolean'],
            'refrigerator' => ['nullable', 'boolean'],
            'security_system' => ['nullable', 'boolean'],
            'swimming_pool' => ['nullable', 'boolean'],
            'tennis_court' => ['nullable', 'boolean'],
            'tv_cable' => ['nullable', 'boolean'],
            'wifi' => ['nullable', 'boolean'],
            'washer' => ['nullable', 'boolean'],
            'location_map' => ['nullable', 'string'],
            'video_url' => ['nullable', 'url'],
        ]);

        DB::beginTransaction();

        try {
            $property = Property::findOrFail($id);

            // Update scalar fields
            $property->project_id = $validated['project_id'] ?? $property->project_id;
            $property->property_type_id = $validated['property_type_id'] ?? $property->property_type_id;
            $property->city_id = $validated['city_id'] ?? $property->city_id;
            $property->developer_id = $validated['developer_id'] ?? $property->developer_id;
            $property->name = $validated['name'];
            $property->slug = Str::slug($validated['name']);
            $property->description = $validated['description'] ?? $property->description;
            $property->address = $validated['address'] ?? $property->address;
            $property->price = $validated['price'] ?? $property->price;
            $property->size = $validated['size'] ?? $property->size;
            $property->is_featured = $validated['is_featured'] ?? $property->is_featured;
            $property->status = $validated['status'] ?? $property->status;
            $property->bedrooms = $validated['bedrooms'] ?? $property->bedrooms;
            $property->bathrooms = $validated['bathrooms'] ?? $property->bathrooms;
            $property->garages = $validated['garages'] ?? $property->garages;
            $property->location_map = $validated['location_map'] ?? $property->location_map;
            $property->video_url = $validated['video_url'] ?? $property->video_url;

            // Update booleans
            $booleans = [
                'air_conditioning',
                'alarm',
                'balcony',
                'cable_tv',
                'central_heating',
                'dryer',
                'dishwasher',
                'garage',
                'gym',
                'library',
                'laundry_room',
                'microwave',
                'oven',
                'parking',
                'pets_allowed',
                'refrigerator',
                'security_system',
                'swimming_pool',
                'tennis_court',
                'tv_cable',
                'wifi',
                'washer'
            ];
            foreach ($booleans as $field) {
                $property->$field = $validated[$field] ?? $property->$field;
            }

            // Update main image: replace if new file provided
            if ($request->hasFile('image')) {
                $property->image = $this->storeFile($request->file('image'), 'properties', $property->image);
            }

            // Update gallery images: replace entirely if any new files are uploaded
            if ($request->hasFile('gallery_images')) {
                // Delete old gallery images
                if ($property->gallery_images) {
                    $oldGallery = json_decode($property->gallery_images, true) ?? [];
                    foreach ($oldGallery as $path) {
                        $this->deleteFile($path);
                    }
                }
                // Store new ones
                $newGallery = [];
                foreach ($request->file('gallery_images') as $file) {
                    $path = $this->storeFile($file, 'properties/gallery');
                    if ($path) {
                        $newGallery[] = $path;
                    }
                }
                $property->gallery_images = !empty($newGallery) ? json_encode($newGallery) : null;
            }

            // Update floor plan images: replace entirely if any new files are uploaded
            if ($request->hasFile('floor_plan_images')) {
                if ($property->floor_plan_images) {
                    $oldFloorPlans = json_decode($property->floor_plan_images, true) ?? [];
                    foreach ($oldFloorPlans as $path) {
                        $this->deleteFile($path);
                    }
                }
                $newFloorPlans = [];
                foreach ($request->file('floor_plan_images') as $file) {
                    $path = $this->storeFile($file, 'properties/floor_plans');
                    if ($path) {
                        $newFloorPlans[] = $path;
                    }
                }
                $property->floor_plan_images = !empty($newFloorPlans) ? json_encode($newFloorPlans) : null;
            }

            $property->save();

            DB::commit();

            return redirect()->route('panel.properties.index')
                ->with('success', 'Property updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PropertyController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong while updating the property.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $property = Property::findOrFail($id);

            // Delete main image
            if ($property->image) {
                $this->deleteFile($property->image);
            }

            // Delete gallery images
            if ($property->gallery_images) {
                $gallery = json_decode($property->gallery_images, true) ?? [];
                foreach ($gallery as $path) {
                    $this->deleteFile($path);
                }
            }

            // Delete floor plan images
            if ($property->floor_plan_images) {
                $floorPlans = json_decode($property->floor_plan_images, true) ?? [];
                foreach ($floorPlans as $path) {
                    $this->deleteFile($path);
                }
            }

            $property->delete();

            DB::commit();

            return redirect()->route('panel.properties.index')
                ->with('success', 'Property deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PropertyController@destroy: ' . $e->getMessage(), [
                'property_id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->with('error', 'Something went wrong while deleting the property.');
        }
    }

    /**
     * Change the status or featured flag of the specified resource.
     *
     * @param Request $request
     * @param string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function status(Request $request, string $id)
    {
        $request->validate([
            'type' => ['required', 'in:status,is_featured'],
        ]);

        DB::beginTransaction();

        try {
            $property = Property::findOrFail($id);

            if ($request->type === 'status') {
                $property->status = !$property->status;
            } elseif ($request->type === 'is_featured') {
                $property->is_featured = !$property->is_featured;
            }

            $property->save();
            DB::commit();

            return redirect()->route('panel.properties.index')
                ->with('success', 'Property status updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PropertyController@status: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong while updating the property status.');
        }
    }
}