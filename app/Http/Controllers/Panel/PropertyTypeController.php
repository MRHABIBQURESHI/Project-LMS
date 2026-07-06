<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\PropertyType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Traits\StoreFileTrait;

class PropertyTypeController extends Controller
{
    use StoreFileTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $propertyTypes = PropertyType::all();
        return view('panel.property.type.index', compact('propertyTypes'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('panel.property.type.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            $propertyType = new PropertyType();
            $propertyType->name = $validated['name'];
            $propertyType->slug = Str::slug($validated['name']);
            $propertyType->description = $validated['description'] ?? null;
            $propertyType->is_featured = $validated['is_featured'] ?? false;
            $propertyType->status = $validated['status'] ?? true;

            // Store icon and image using trait
            $propertyType->icon = $this->storeFile($request->file('icon'), 'property_types') ?? null;
            $propertyType->image = $this->storeFile($request->file('image'), 'property_types') ?? null;

            $propertyType->save();

            DB::commit();

            return redirect()->route('panel.property.type.index')
                ->with('success', 'Property type created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PropertyTypeController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong while creating the property type.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $propertyType = PropertyType::findOrFail($id);
        return view('panel.property.type.edit', compact('propertyType'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'icon' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg', 'max:2048'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            $propertyType = PropertyType::findOrFail($id);

            $propertyType->name = $validated['name'];
            $propertyType->slug = Str::slug($validated['name']);
            $propertyType->description = $validated['description'] ?? $propertyType->description;
            $propertyType->is_featured = $validated['is_featured'] ?? $propertyType->is_featured;
            $propertyType->status = $validated['status'] ?? $propertyType->status;

            // Update files: pass old paths for deletion when replacing
            $propertyType->icon = $this->storeFile($request->file('icon'), 'property_types', $propertyType->icon);
            $propertyType->image = $this->storeFile($request->file('image'), 'property_types', $propertyType->image);

            $propertyType->save();

            DB::commit();

            return redirect()->route('panel.property.type.index')
                ->with('success', 'Property type updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PropertyTypeController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong while updating the property type.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $propertyType = PropertyType::findOrFail($id);

            // Delete associated files
            if ($propertyType->icon) {
                $this->deleteFile($propertyType->icon);
            }
            if ($propertyType->image) {
                $this->deleteFile($propertyType->image);
            }

            $propertyType->delete();

            DB::commit();

            return redirect()->route('panel.property.type.index')
                ->with('success', 'Property type deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PropertyTypeController@destroy: ' . $e->getMessage(), [
                'property_type_id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->with('error', 'Something went wrong while deleting the property type.');
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
            $propertyType = PropertyType::findOrFail($id);

            if ($request->type === 'status') {
                $propertyType->status = !$propertyType->status;
            } elseif ($request->type === 'is_featured') {
                $propertyType->is_featured = !$propertyType->is_featured;
            }

            $propertyType->save();
            DB::commit();

            return redirect()->route('panel.property.type.index')
                ->with('success', 'Property type status updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('PropertyTypeController@status: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong while updating the property type status.');
        }
    }
}