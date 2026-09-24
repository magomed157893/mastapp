<?php

namespace App\Http\Controllers;

use App\Models\Referral;
use App\Models\ReferralEarning;
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

        $validated = $request->validate(['code' => 'required|string']);

        $referral = $this->referrals->registerReferral($master, $validated['code']);
        if (is_null($referral)) {
            return response()->json(
                ['message' => 'Refferal cannot be attached'],
                Response::HTTP_UNPROCESSABLE_ENTITY
            );
        }

        return response()->json(
            ['referral' => $referral],
            $referral->wasRecentlyCreated ? Response::HTTP_CREATED : Response::HTTP_OK
        );
    }

    public function my(Request $request): Response
    {
        /** @var \App\Models\Master */
        $master = $request->attributes->get('current_master');

        $referrals = $master->referrals()
            ->with(['referredMaster', 'referralEarnings'])
            ->get();

        return response()->json([
            'referrals' => $referrals->map(function (Referral $referral) {
                return [
                    'name' => $referral->referredMaster->name,
                    'attached_at' => $referral->created_at,
                    'rewarded' => $referral->status === Referral::STATUS_REWARDED,
                    'earned' => $referral->referralEarnings->sum('amount')
                ];
            })
        ]);
    }

    public function earnings(Request $request): Response
    {
        /** @var \App\Models\Master */
        $master = $request->attributes->get('current_master');

        $total = $master->referralEarnings()->sum('amount');

        $pending = $master->referralEarnings()
            ->where('status', ReferralEarning::STATUS_PENDING)
            ->sum('amount');

        $paid = $master->referralEarnings()
            ->where('status', ReferralEarning::STATUS_PAID)
            ->sum('amount');

        $rewardedReferrals = $master->referrals()->active()->count();

        return response()->json([
            'total' => $total,
            'pending' => $pending,
            'paid' => $paid,
            'rewarded_referrals' => $rewardedReferrals
        ]);
    }
}
