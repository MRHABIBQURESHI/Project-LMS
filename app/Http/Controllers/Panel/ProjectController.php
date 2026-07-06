<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Traits\StoreFileTrait;

class ProjectController extends Controller
{
    use StoreFileTrait;

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $projects = Project::all();
        return view('panel.projects.index', compact('projects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('panel.projects.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'city_id' => ['nullable', 'exists:cities,id'],
            'developer_id' => ['nullable', 'exists:developers,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'completion_year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 10)],
            'starting_price' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            $project = new Project();
            $project->city_id = $validated['city_id'] ?? null;
            $project->developer_id = $validated['developer_id'] ?? null;
            $project->name = $validated['name'];
            $project->slug = Str::slug($validated['name']);
            $project->description = $validated['description'] ?? null;
            $project->location = $validated['location'] ?? null;
            $project->completion_year = $validated['completion_year'] ?? null;
            $project->starting_price = $validated['starting_price'] ?? null;
            $project->is_featured = $validated['is_featured'] ?? false;
            $project->status = $validated['status'] ?? true;

            // Store images using trait
            $project->image = $this->storeFile($request->file('image'), 'projects') ?? null;
            $project->banner_image = $this->storeFile($request->file('banner_image'), 'projects') ?? null;

            $project->save();

            DB::commit();

            return redirect()->route('panel.projects.index')
                ->with('success', 'Project created successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ProjectController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong while creating the project.');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $project = Project::findOrFail($id);
        return view('panel.projects.edit', compact('project'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $validated = $request->validate([
            'city_id' => ['nullable', 'exists:cities,id'],
            'developer_id' => ['nullable', 'exists:developers,id'],
            'name' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:255'],
            'completion_year' => ['nullable', 'integer', 'min:1900', 'max:' . (date('Y') + 10)],
            'starting_price' => ['nullable', 'numeric', 'min:0'],
            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'banner_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
            'is_featured' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            $project = Project::findOrFail($id);

            $project->city_id = $validated['city_id'] ?? $project->city_id;
            $project->developer_id = $validated['developer_id'] ?? $project->developer_id;
            $project->name = $validated['name'];
            $project->slug = Str::slug($validated['name']);
            $project->description = $validated['description'] ?? $project->description;
            $project->location = $validated['location'] ?? $project->location;
            $project->completion_year = $validated['completion_year'] ?? $project->completion_year;
            $project->starting_price = $validated['starting_price'] ?? $project->starting_price;
            $project->is_featured = $validated['is_featured'] ?? $project->is_featured;
            $project->status = $validated['status'] ?? $project->status;

            // Update images: pass old paths for deletion when replacing
            $project->image = $this->storeFile($request->file('image'), 'projects', $project->image);
            $project->banner_image = $this->storeFile($request->file('banner_image'), 'projects', $project->banner_image);

            $project->save();

            DB::commit();

            return redirect()->route('panel.projects.index')
                ->with('success', 'Project updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ProjectController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong while updating the project.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {
            $project = Project::findOrFail($id);

            // Delete associated images
            if ($project->image) {
                $this->deleteFile($project->image);
            }
            if ($project->banner_image) {
                $this->deleteFile($project->banner_image);
            }

            $project->delete();

            DB::commit();

            return redirect()->route('panel.projects.index')
                ->with('success', 'Project deleted successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ProjectController@destroy: ' . $e->getMessage(), [
                'project_id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);
            return back()->with('error', 'Something went wrong while deleting the project.');
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
            $project = Project::findOrFail($id);

            if ($request->type === 'status') {
                $project->status = !$project->status;
            } elseif ($request->type === 'is_featured') {
                $project->is_featured = !$project->is_featured;
            }

            $project->save();
            DB::commit();

            return redirect()->route('panel.projects.index')
                ->with('success', 'Project status updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('ProjectController@status: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong while updating the project status.');
        }
    }
}