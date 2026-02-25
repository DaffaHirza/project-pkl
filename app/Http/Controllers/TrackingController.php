<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TrackingController extends Controller
{
    /**
     * Display the tracking dashboard.
     */
    public function index()
    {
        return view('tracking.index');
    }
}
