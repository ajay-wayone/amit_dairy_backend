<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class PolicyController extends Controller
{
    // Privacy Policy
    public function privacy()
    {
        $policy = DB::table('privacy_policy')->first();

        if (!$policy) {
            return response('Privacy policy not found', 404);
        }

        // ✅ HTML OUTPUT render hoga (code show nahi hoga)
        return response($policy->content)
            ->header('Content-Type', 'text/html');
    }

    // Refund Policy
    public function refund()
    {
        $policy = DB::table('refund_policy')->first();

        if (!$policy) {
            return response('Refund policy not found', 404);
        }

        return response($policy->content)
            ->header('Content-Type', 'text/html');
    }

    // Terms & Conditions (agar table hai)
    public function terms()
    {
        $policy = DB::table('terms_conditions')->first();

        if (!$policy) {
            return response('Terms not found', 404);
        }

        return response($policy->content)
            ->header('Content-Type', 'text/html');
    }
}
