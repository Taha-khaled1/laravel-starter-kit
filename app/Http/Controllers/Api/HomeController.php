<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\QrCodeService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
class HomeController extends Controller
{
    protected $qrCodeService;

    public function __construct(QrCodeService $qrCodeService)
    {
        $this->qrCodeService = $qrCodeService;
    }
    public function index(Request $request)
    {

        Log::info('HomeController: Loading index page', ['user_id' => Auth::guard('sanctum')->id()]);
        // Get current locale or default
        $locale = app()->getLocale();
        $user = Auth::guard('sanctum')->user();
    
        return successResponse([
          

        ]);
    }

    
    
}
