<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Developer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Traits\StoreFileTrait;

class DeveloperController extends Controller
{
    use StoreFileTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $developers = Developer::all();
        return view('panel.developers.index', compact('developers'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('panel.developers.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website_url' => ['nullable', 'url'],
            'number' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            $developer = new Developer();
            $developer->name = $validated['name'];
            $developer->slug = Str::slug($validated['name']);
            $developer->description = $validated['description'] ?? null;
            $developer->website_url = $validated['website_url'] ?? null;
            $developer->number = $validated['number'] ?? null;
            $developer->email = $validated['email'] ?? null;
            $developer->status = $validated['status'] ?? false;

            // Store logo using trait
            $developer->logo = $this->storeFile($request->file('logo'), 'developers') ?? null;

            $developer->save();

            DB::commit();

            return redirect()->route('panel.developers.index')
                ->with('success', 'Developer created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DeveloperController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong while creating the developer.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $developer = Developer::findOrFail($id);
        return view('panel.developers.edit', compact('developer'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'website_url' => ['nullable', 'url'],
            'number' => ['nullable', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'logo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'status' => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            $developer = Developer::findOrFail($id);

            $developer->name = $validated['name'];
            $developer->slug = Str::slug($validated['name']);
            $developer->description = $validated['description'] ?? $developer->description;
            $developer->website_url = $validated['website_url'] ?? $developer->website_url;
            $developer->number = $validated['number'] ?? $developer->number;
            $developer->email = $validated['email'] ?? $developer->email;
            $developer->status = $validated['status'] ?? $developer->status;

            // Update logo: pass old file path for deletion when replacing
            $developer->logo = $this->storeFile($request->file('logo'), 'developers', $developer->logo);

            $developer->save();

            DB::commit();

            return redirect()->route('panel.developers.index')
                ->with('success', 'Developer updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DeveloperController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong while updating the developer.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $developer = Developer::findOrFail($id);

            // Delete logo if exists
            if ($developer->logo) {
                $this->deleteFile($developer->logo);
            }

            $developer->delete();

            DB::commit();

            return redirect()->route('panel.developers.index')
                ->with('success', 'Developer deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DeveloperController@destroy: ' . $e->getMessage(), [
                'developer_id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->with('error', 'Something went wrong while deleting the developer.');
        }
    }

    /**
     * Change the status of the specified resource.
     * 
     * @param Request $request
     * @param string  $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function status(Request $request, string $id)
    {
        $request->validate([
            'type' => ['required', 'in:status'],
        ]);

        DB::beginTransaction();

        try {
            $developer = Developer::findOrFail($id);

            // Toggle the status
            $developer->status = !$developer->status;
            $developer->save();

            DB::commit();

            return redirect()->route('panel.developers.index')
                ->with('success', 'Developer status updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('DeveloperController@status: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong while updating the developer status.');
        }
    }
}