<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SettingWeb;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SettingPageController extends Controller
{
    public function termsPage()
    {
        try {
            $terms = SettingWeb::select(DB::raw("terms_" . app()->getLocale() . " AS terms"))->first();
            return  response()->json([
                'data' => $terms->terms,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to retrieve data.', 'status_code' => 500], 500);
        }
    }

    public function aboutPage()
    {
        try {
            $about_us = SettingWeb::select(DB::raw("about_us_" . app()->getLocale() . " AS about_us"))->first();
            return response()->json([
                'data' => $about_us->about_us,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to retrieve data.', 'status_code' => 500], 500);
        }
    }


    public function privacyPage()
    {
        try {
            $privacy = SettingWeb::select(DB::raw("privacy_" . app()->getLocale() . " AS privacy"))->first();
            return response()->json([
                'data' => $privacy->privacy,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to retrieve data.', 'status_code' => 500], 500);
        }
    }


    public function returnPolicyPage()
    {
        try {
            $return_policy = SettingWeb::select(DB::raw("return_policy_" . app()->getLocale() . " AS return_policy"))->first();
            return response()->json([
                'data' => $return_policy->return_policy,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to retrieve data.' . $th, 'status_code' => 500], 500);
        }
    }

    public function storePolicyPage()
    {
        try {
            $store_policy = SettingWeb::select(DB::raw("store_policy_" . app()->getLocale() . " AS store_policy"))->first();
            return response()->json([
                'data' => $store_policy->store_policy,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to retrieve data.', 'status_code' => 500], 500);
        }
    }


    public function sellerPolicyPage()
    {
        try {
            $seller_policy = SettingWeb::select(DB::raw("seller_policy_" . app()->getLocale() . " AS seller_policy"))->first();
            return response()->json([
                'data' => $seller_policy->seller_policy,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to retrieve data.', 'status_code' => 500], 500);
        }
    }


    public function primeryColor()
    {
        try {
            $colors = SettingWeb::select("color_primery", 'color_second_primery')->first();
            return response()->json([
                'colors' => $colors,
                'message' => 'Success',
                'status_code' => 200
            ], 200);
        } catch (\Throwable $th) {
            return response()->json(['message' => 'Failed to retrieve data.', 'status_code' => 500], 500);
        }
    }
}
