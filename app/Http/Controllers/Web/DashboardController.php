<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Services\DashboardService;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __construct(
        private readonly DashboardService $dashboardService
    ) {}

    public function index(): View
    {
        $stats = $this->dashboardService->getDashboardData(auth()->user());

        return view('dashboard.index', [
            'stats' => $stats,
            'isAdmin' => auth()->user()->isAdmin(),
        ]);
    }
}
