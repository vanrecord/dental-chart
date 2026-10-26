<?php

namespace Modules\Core\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class CoreController extends Controller
{
    /**
     * Display the home dashboard.
     */
    public function index(): View
    {
        return view('core::welcome');
    }
}
