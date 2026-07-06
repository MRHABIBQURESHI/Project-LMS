<?php

namespace App\Http\Controllers\Panel;

use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Traits\StoreFileTrait;

class AgentController extends Controller
{
    use StoreFileTrait;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $agents = Agent::all();
        return view('panel.agents.index', compact('agents'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('panel.agents.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'city_id' => ['nullable', 'exists:cities,id'],
            'developer_id' => ['nullable', 'exists:developers,id'],
            'property_type_id' => ['nullable', 'exists:property_types,id'],
            'project_id' => ['nullable', 'exists:projects,id'],

            'name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'facebook' => ['nullable', 'url'],
            'twitter' => ['nullable', 'url'],
            'instagram' => ['nullable', 'url'],
            'linkedin' => ['nullable', 'url'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'other_link' => ['nullable', 'url'],

            'is_featured' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {

            $agent = new Agent();
            $agent->city_id = $validated['city_id'] ?? null;
            $agent->developer_id = $validated['developer_id'] ?? null;
            $agent->property_type_id = $validated['property_type_id'] ?? null;
            $agent->project_id = $validated['project_id'] ?? null;
            $agent->name = $validated['name'];
            $agent->slug = Str::slug($validated['name']);
            $agent->designation = $validated['designation'] ?? null;
            $agent->description = $validated['description'] ?? null;
            $agent->facebook = $validated['facebook'] ?? null;
            $agent->twitter = $validated['twitter'] ?? null;
            $agent->instagram = $validated['instagram'] ?? null;
            $agent->linkedin = $validated['linkedin'] ?? null;
            $agent->whatsapp = $validated['whatsapp'] ?? null;
            $agent->other_link = $validated['other_link'] ?? null;
            $agent->is_featured = $validated['is_featured'] ?? false;
            $agent->status = $validated['status'] ?? false;
            $agent->image = $this->storeFile($request->image, 'agents') ?? null;

            $agent->save();
            DB::commit();

            return redirect()->route('panel.agents.index')->with('success', 'Agent created successfully.');

        } catch (\Throwable $e) {

            DB::rollBack();
            Log::error('AgentController@store: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong while creating the agent.');
        }
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $agent = Agent::findOrFail($id);
        return view('panel.agents.edit', compact('agent'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'city_id' => ['nullable', 'exists:cities,id'],
            'developer_id' => ['nullable', 'exists:developers,id'],
            'property_type_id' => ['nullable', 'exists:property_types,id'],
            'project_id' => ['nullable', 'exists:projects,id'],

            'name' => ['required', 'string', 'max:255'],
            'designation' => ['nullable', 'string', 'max:255'],
            'description' => ['nullable', 'string'],

            'image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],

            'facebook' => ['nullable', 'url'],
            'twitter' => ['nullable', 'url'],
            'instagram' => ['nullable', 'url'],
            'linkedin' => ['nullable', 'url'],
            'whatsapp' => ['nullable', 'string', 'max:20'],
            'other_link' => ['nullable', 'url'],

            'is_featured' => ['nullable', 'boolean'],
            'status' => ['nullable', 'boolean'],
        ]);

        DB::beginTransaction();

        try {
            $agent = Agent::findOrFail($id);

            $agent->city_id = $validated['city_id'] ?? $agent->city_id;
            $agent->developer_id = $validated['developer_id'] ?? $agent->developer_id;
            $agent->property_type_id = $validated['property_type_id'] ?? $agent->property_type_id;
            $agent->project_id = $validated['project_id'] ?? $agent->project_id;
            $agent->name = $validated['name'] ?? $agent->name;
            $agent->slug = Str::slug($validated['name']) ?? $agent->slug;
            $agent->designation = $validated['designation'] ?? $agent->designation;
            $agent->description = $validated['description'] ?? $agent->description;
            $agent->facebook = $validated['facebook'] ?? $agent->facebook;
            $agent->twitter = $validated['twitter'] ?? $agent->twitter;
            $agent->instagram = $validated['instagram'] ?? $agent->instagram;
            $agent->linkedin = $validated['linkedin'] ?? $agent->linkedin;
            $agent->whatsapp = $validated['whatsapp'] ?? $agent->whatsapp;
            $agent->other_link = $validated['other_link'] ?? $agent->other_link;
            $agent->is_featured = $validated['is_featured'] ?? $agent->is_featured;
            $agent->status = $validated['status'] ?? $agent->status;
            $agent->image = $this->storeFile($request->image, 'agents', $agent->image ?? null);
            $agent->save();

            DB::commit();

            return redirect()->route('panel.agents.index')->with('success', 'Agent updated successfully.');
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('AgentController@update: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Something went wrong while updating the agent.');
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        DB::beginTransaction();

        try {

            $agent = Agent::findOrFail($id);

            if ($agent->image) {
                $this->deleteFile($agent->image);
            }

            $agent->delete();

            DB::commit();

            return redirect()
                ->route('panel.agents.index')
                ->with('success', 'Agent deleted successfully.');

        } catch (\Throwable $e) {

            DB::rollBack();

            Log::error('AgentController@destroy: ' . $e->getMessage(), [
                'agent_id' => $id,
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return back()
                ->with('error', 'Something went wrong while deleting the agent.');
        }
    }

    /**
     * Change the status of the specified resource.
     * @param type => status or featured
     * @param status => 1 = Active, 0 = Inactive
     * @param is_featured => 1 = Featured, 0 = Not Featured
     */
    public function status(Request $request, string $id)
    {
        $request->validate([
            'type' => ['required', 'in:status,is_featured'],
        ]);

        DB::beginTransaction();

        try {
            $agent = Agent::findOrFail($id);
            if ($request->type === 'status') {
                $agent->status = !$agent->status;
            }

            if ($request->type === 'is_featured') {
                $agent->is_featured = !$agent->is_featured;
            }

            $agent->save();
            DB::commit();
            return redirect()->route('panel.agents.index')->with('success', 'Agent updated successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('AgentController@status: ' . $e->getMessage());
            return back()->with('error', 'Something went wrong while updating the agent.');
        }
    }
}
