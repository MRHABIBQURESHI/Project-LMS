<?php

namespace App\View\Components\Partials;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

use App\Models\Project;

class Footer extends Component
{
    public $featured_projects;

    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->featured_projects = Project::with(['city'])
            ->where('is_featured', true)
            ->where('status', true)
            ->latest()
            ->take(5)
            ->get();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.partials.footer');
    }
}
