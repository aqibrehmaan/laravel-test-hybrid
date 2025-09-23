<?php

namespace App\Services;

use App\Exceptions\AffiliateCreateException;
use App\Mail\AffiliateCreated;
use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Mail;

class AffiliateService
{
    public function __construct(
        protected ApiService $apiService
    ) {}

    /**
     * Create a new affiliate for the merchant with the given commission rate.
     *
     * @param  Merchant $merchant
     * @param  string $email
     * @param  string $name
     * @param  float $commissionRate
     * @return Affiliate
     */
    public function register(Merchant $merchant, string $email, string $name, float $commissionRate): Affiliate
    {
        // TODO: Complete this method
        $user = User::where('email', $email)
                    ->where(function ($q) {
                        $q->where('type', User::TYPE_MERCHANT)->orWhere('type', User::TYPE_AFFILIATE);
                    })->first();

        if($user?->type == User::TYPE_MERCHANT) {
            throw new AffiliateCreateException('Email is associated with a merchant');
        } else if($user?->type == User::TYPE_AFFILIATE) {
            throw new AffiliateCreateException('Email is associated with a Affiliate');
        }

        $affiliate = Affiliate::create([
            'user_id' => $merchant->user->id,
            'merchant_id' => $merchant->id,
            'commission_rate' => $commissionRate,
            'discount_code' => $this->apiService->createDiscountCode($merchant)['code']
        ]);

        Mail::to($email)->send(new AffiliateCreated($affiliate));

        User::create([
            'name' => $name,
            'email' => $email,
            'type' => User::TYPE_AFFILIATE
        ]);

        return $affiliate;
    }
}
