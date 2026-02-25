<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AssistantController extends Controller
{
    /**
     * Display the AI Assistant dashboard.
     */
    public function index()
    {
        return view('assistant.index');
    }
}
