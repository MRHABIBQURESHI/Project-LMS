<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Traits\StoreFileTrait;

class CityController extends Controller
{
    use StoreFileTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $cities = City::all();
        return view('panel.cities.index', compact('cities'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('panel.cities.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            $city = new City();
            $city->name = $validated['name'];
            $city->slug = Str::slug($validated['name']);
            $city->state = $validated['state'] ?? null;
            $city->country = $validated['country'] ?? 'Pakistan';
            $city->is_featured = $validated['is_featured'] ?? false;
            $city->status = $validated['status'] ?? true;

            // Store images using trait
            $city->image = $this->storeFile($request->file('image'), 'cities') ?? null;
            $city->banner_image = $this->storeFile($request->file('banner_image'), 'cities') ?? null;

            $city->save();

            DB::commit();

            return redirect()->route('panel.cities.index')
                ->with('success', 'City created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('CityController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong while creating the city.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $city = City::findOrFail($id);
        return view('panel.cities.edit', compact('city'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'state' => ['nullable', 'string', 'max:255'],
            'country' => ['nullable', 'string', 'max:255'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            $city = City::findOrFail($id);

            $city->name = $validated['name'];
            $city->slug = Str::slug($validated['name']);
            $city->state = $validated['state'] ?? $city->state;
            $city->country = $validated['country'] ?? $city->country;
            $city->is_featured = $validated['is_featured'] ?? $city->is_featured;
            $city->status = $validated['status'] ?? $city->status;

            // Update images: pass old file paths for deletion when replacing
            $city->image = $this->storeFile($request->file('image'), 'cities', $city->image);
            $city->banner_image = $this->storeFile($request->file('banner_image'), 'cities', $city->banner_image);

            $city->save();

            DB::commit();

            return redirect()->route('panel.cities.index')
                ->with('success', 'City updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('CityController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong while updating the city.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $city = City::findOrFail($id);

            // Delete associated images
            if ($city->image) {
                $this->deleteFile($city->image);
            }
            if ($city->banner_image) {
                $this->deleteFile($city->banner_image);
            }

            $city->delete();

            DB::commit();

            return redirect()->route('panel.cities.index')
                ->with('success', 'City deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('CityController@destroy: ' . $e->getMessage(), [
                'city_id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->with('error', 'Something went wrong while deleting the city.');
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
            $city = City::findOrFail($id);

            if ($request->type === 'status') {
                $city->status = !$city->status;
            } elseif ($request->type === 'is_featured') {
                $city->is_featured = !$city->is_featured;
            }

            $city->save();
            DB::commit();

            return redirect()->route('panel.cities.index')
                ->with('success', 'City status updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('CityController@status: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong while updating the city status.');
        }
    }
}