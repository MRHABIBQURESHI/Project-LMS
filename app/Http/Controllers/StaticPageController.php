<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Property;
use App\Models\PropertyType;
use App\Models\Agent;
use App\Models\Developer;
use Illuminate\Http\Request;

class StaticPageController extends Controller
{
    public function index(Request $request)
    {
        $page_title = "Home";
        $poular_projects = Project::with(['city'])
            ->where('is_featured', true)->where('status', true)
            ->inRandomOrder()->take(8)->get();

        // Featured Properties
        $featured_properties = Property::with(['city', 'project', 'propertyType'])
            ->where('is_featured', true)->where('status', true)
            ->inRandomOrder()->take(9)
            ->get();

        // Cities with their project counts
        $cities = City::withCount('projects')->where('status', true)
            ->where('is_featured', true)
            ->inRandomOrder()->get();

        // Property categories (from property types)
        $types = PropertyType::where('status', true)->where('is_featured', true)
            ->take(6)->get();

        // Fetch agents instead of teams
        $agents = Agent::with(['city', 'project', 'developer', 'propertyType'])
            ->where('status', true)
            ->take(3)
            ->inRandomOrder()
            ->get();
        $teams = $agents;

        // Partners
        $partners = Developer::where('status', true)
            ->take(5)
            ->inRandomOrder()
            ->get();

        // All Projects for search dropdown
        $all_projects = Project::select('name', 'slug')->where('status', true)->orderBy('name')->get();

        return view("index", compact(
            'poular_projects',
            'featured_properties',
            'cities',
            'types',
            'teams',
            'agents',
            'partners',
            'page_title',
            'all_projects'
        ));
    }

    public function about(Request $request)
    {
        $page_title = "About";
        $poular_projects = Project::with(['city'])
            ->where('is_featured', true)->where('status', true)
            ->inRandomOrder()->take(8)->get();

        // Featured Properties
        $featured_properties = Property::with(['city', 'project', 'propertyType'])
            ->where('is_featured', true)->where('status', true)
            ->inRandomOrder()->take(9)
            ->get();

        // Cities with their project counts
        $cities = City::withCount('projects')->where('status', true)
            ->where('is_featured', true)
            ->inRandomOrder()->get();

        // Property categories (from property types)
        $types = PropertyType::where('status', true)->where('is_featured', true)
            ->take(6)->get();

        // Fetch agents instead of teams
        $agents = Agent::with(['city', 'project', 'developer', 'propertyType'])
            ->where('status', true)
            ->take(3)
            ->inRandomOrder()
            ->get();
        $teams = $agents;

        // Partners
        $partners = Developer::where('status', true)
            ->take(5)
            ->inRandomOrder()
            ->get();

        return view("about", compact(
            'page_title',
            'poular_projects',
            'featured_properties',
            'cities',
            'types',
            'agents',
            'partners',
        ));
    }

    public function contact(Request $request)
    {
        $page_title = "Contact Us";
        return view("contact", compact('page_title'));
    }

    public function allProjects(Request $request, $city_slug = null)
    {
        $projects = Project::selectRaw("projects.*,
            cities.name AS city_name,
            cities.slug AS city_slug,
            developers.name AS developer,
            COUNT(properties.id) AS total_properties")
            ->leftJoin('cities', 'cities.id', '=', 'projects.city_id')
            ->leftJoin('developers', 'developers.id', '=', 'projects.developer_id')
            ->leftJoin('properties', function ($join) {
                $join->on('properties.project_id', '=', 'projects.id')
                    ->where('properties.status', true);
            })
            ->where('projects.status', true)
            ->groupBy("projects.id", "cities.name", "cities.slug", "developers.name")
            ->when($city_slug, function ($query) use ($city_slug) {
                $query->where('cities.slug', $city_slug);
            })
            ->when($request->city, function ($query) use ($request) {
                $query->where('cities.slug', $request->city);
            })
            ->when($request->type, function ($query) use ($request) {
                $query->whereExists(function ($sub) use ($request) {
                    $sub->select(\DB::raw(1))
                        ->from('properties')
                        ->join('property_types', 'property_types.id', '=', 'properties.property_type_id')
                        ->whereColumn('properties.project_id', 'projects.id')
                        ->where('property_types.slug', $request->type)
                        ->where('properties.status', true);
                });
            })
            ->when($request->project, function ($query) use ($request) {
                $query->where('projects.slug', $request->project);
            })
            ->latest()
            ->paginate(12);

        $cities = City::where('status', true)->get();
        $types = PropertyType::where('status', true)->get();

        // Filter by project slug if provided
        if ($request->project) {
            $projects->appends(['project' => $request->project]);
        }

        return view("projects.index", compact('projects', 'cities', 'types'));
    }

    public function projectProperties(Request $request, $project_slug)
    {
        $project = Project::with(['city', 'developerRelation'])
            ->where('slug', $project_slug)
            ->where('status', true)
            ->firstOrFail();

        $properties = Property::selectRaw("
            properties.*,
            projects.name AS project_name,
            projects.slug AS project_slug,
            cities.slug AS city_slug,
            cities.name AS city_name,
            developers.name AS developer_name")
            ->leftJoin('projects', 'projects.id', '=', 'properties.project_id')
            ->leftJoin('cities', 'cities.id', '=', 'properties.city_id')
            ->leftJoin('property_types', 'property_types.id', '=', 'properties.property_type_id')
            ->leftJoin('developers', 'developers.id', '=', 'projects.developer_id')
            ->where('properties.project_id', $project->id)
            ->where('properties.status', true)
            ->when($request->q, function ($query) use ($request) {
                $query->where(function($q) use ($request) {
                    $q->where('properties.name', 'like', "%{$request->q}%")
                        ->orWhere('properties.slug', 'like', "%{$request->q}%");
                });
            })
            ->when($request->city_id, function ($query) use ($request) {
                $query->where('properties.city_id', $request->city_id);
            })
            ->when($request->property_type_id, function ($query) use ($request) {
                $query->where('properties.property_type_id', $request->property_type_id);
            })
            ->when($request->project_id, function ($query) use ($request) {
                $query->where('properties.project_id', $request->project_id);
            })
            ->latest()
            ->paginate(6);

        $featured_projects = Project::with(['city'])->withCount('properties')
            ->where('is_featured', true)
            ->where('status', true)
            ->where('slug', '!=', $project->slug)
            ->latest()
            ->take(3)
            ->get();

        $projects = Project::select('name', 'id')->where('status', true)->get();
        $cities = City::select('name', 'id')->where('status', true)->get();
        $propertyTypes = PropertyType::select('name', 'id')->where('status', true)->get();

        // Map fields to mimic expected $data structure for detail.blade.php
        $project->project_name = $project->name;
        $project->city_name = $project->city ? $project->city->name : 'N/A';
        $project->developer_name = $project->developerRelation ? $project->developerRelation->name : 'N/A';
        $data = $project;

        return view("projects.detail", compact('project', 'properties', 'data', 'featured_projects', 'projects', 'cities', 'propertyTypes'));
    }

    public function property_detail(Request $request, $project_slug, $property_slug)
    {
        $proptery = Property::selectRaw("             
            properties.*,
            projects.banner_image AS banner_image,
            projects.name AS project_name,
            cities.slug AS city_slug,
            cities.name AS city_name,
            developers.name AS developer_name,
            projects.slug AS project_slug")
            ->leftJoin('projects', 'projects.id', '=', 'properties.project_id')
            ->leftJoin('cities', 'cities.id', '=', 'properties.city_id')
            ->leftJoin('property_types', 'property_types.id', '=', 'properties.property_type_id')
            ->leftJoin('developers', 'developers.id', '=', 'projects.developer_id')
            ->where('properties.slug', $property_slug)
            ->where('projects.slug', $project_slug)
            ->where('properties.status', true)
            ->where('projects.status', true)
            ->when($request->property_type_id, function ($query) use ($request) {
                $query->where('properties.property_type_id', $request->property_type_id);
            })
            ->when($request->city_id, function ($query) use ($request) {
                $query->where('properties.city_id', $request->city_id);
            })
            ->when($request->project_id, function ($query) use ($request) {
                $query->where('properties.project_id', $request->project_id);
            })
            ->when($request->q, function ($query) use ($request) {
                $search = "%" . $request->q . "%";
                $query->where(function($q) use ($search) {
                    $q->where('properties.name', 'LIKE', $search)
                        ->orWhere('properties.slug', 'LIKE', $search)
                        ->orWhere('projects.name', 'LIKE', $search)
                        ->orWhere('cities.name', 'LIKE', $search)
                        ->orWhere('property_types.name', 'LIKE', $search)
                        ->orWhere('developers.name', 'LIKE', $search);
                });
            })
            ->when($request->property_size, function ($query) use ($request) {
                $query->where('properties.size', $request->property_size);
            })
            ->firstOrFail();

        // dd($proptery);
        $featured_properties = Property::with(['city', 'project', 'propertyType'])
            ->where('is_featured', true)
            ->where('id', '!=', $proptery->id)
            ->where('status', true)
            ->latest()
            ->take(4)
            ->get();

        $projects = Project::select('name', 'id')->where('status', true)->get();
        $cities = City::select('name', 'id')->where('status', true)->get();
        $propertyTypes = PropertyType::select('name', 'id')->where('status', true)->get();
        $propertySizes = Property::select('size')->where('status', true)->distinct()->orderBy('size')->get();

        return view("projects.show", compact('featured_properties', 'proptery', 'projects', 'cities', 'propertyTypes', 'propertySizes'));
    }
}
