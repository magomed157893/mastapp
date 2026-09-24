<?php

namespace App\Http\Controllers;

use App\Services\Referral\ReferralService;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ReferralController extends Controller
{
    public function __construct(
        private ReferralService $referrals
    ) {}

    public function attach(Request $request): Response
    {
        /** @var \App\Models\Master */
        $master = $request->attributes->get('current_master');

        return response()->json(['master' => $master]);
    }

    public function my(Request $request): Response
    {
        /** @var \App\Models\Master */
        $master = $request->attributes->get('current_master');

        return response()->json([]);
    }

    public function earnings(Request $request): Response
    {
        /** @var \App\Models\Master */
        $master = $request->attributes->get('current_master');

        return response()->json([]);
    }
}
