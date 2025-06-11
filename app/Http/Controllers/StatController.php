<?php

namespace App\Http\Controllers;

use App\Services\StatService;
use Illuminate\Http\Request;

class StatController extends Controller
{
    public function __construct(protected StatService $statService)
    {
        
    }

    public function fetchDashboardCardStats(Request $request) {
        return $this->statService->getDashboardCardStats($request);
    }
}
