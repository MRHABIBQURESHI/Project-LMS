<?php

namespace App\Http\Controllers\Lms;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Display the LMS portal landing page.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('lms.home');
    }
}
