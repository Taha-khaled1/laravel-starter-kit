<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Contracts\View\Factory;
use Illuminate\Contracts\View\View;
use Illuminate\Foundation\Application;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class DashboardController extends Controller
{
  public function admin(): View|Factory|Application
  {
    try {
      Log::info('DashboardController: Loading admin dashboard');
      
      // Get counts for key metrics
      $totalUsers = User::where('type', 'user')->count();
      $totalSupervisors = User::where('type', 'supervisor')->count();
      $totalEvents = 9;
      $totalPositions = 9;
      $totalJobPositions = 9;
      $totalApplications = 9;
      
      
        
     
      // User registration trends (last 12 months)
      $userRegistrationTrend = User::select(
          DB::raw('MONTH(created_at) as month'), 
          DB::raw('YEAR(created_at) as year'), 
          DB::raw('count(*) as total')
        )
        ->where('created_at', '>=', Carbon::now()->subMonths(12))
        ->groupBy('year', 'month')
        ->orderBy('year', 'asc')
        ->orderBy('month', 'asc')
        ->get()
        ->map(function ($item) {
          return [
            'month' => Carbon::createFromDate($item->year, $item->month, 1)->format('M Y'),
            'total' => $item->total
          ];
        });
        
      
      
      
      
      Log::info('DashboardController: Admin dashboard statistics loaded successfully');
      
      return view('content.admin.dashboard', compact(
        'totalUsers',
        'totalSupervisors',
        'totalEvents',
        'totalPositions',
        'totalJobPositions',
        'totalApplications',
        
      ));
    } catch (\Exception $e) {
      Log::error('DashboardController: Error loading admin dashboard', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
      ]);
      
      // Return a simplified view if there's an error
      return view('content.admin.dashboard', [
        'error' => $e->getMessage()
      ]);
    }
  }
}
