<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\User;

class OrderService
{
    public function __construct(
        protected AffiliateService $affiliateService
    ) {}

    /**
     * Process an order and log any commissions.
     * This should create a new affiliate if the customer_email is not already associated with one.
     * This method should also ignore duplicates based on order_id.
     *
     * @param  array{order_id: string, subtotal_price: float, merchant_domain: string, discount_code: string, customer_email: string, customer_name: string} $data
     * @return void
     */
    public function processOrder(array $data)
    {
        // TODO: Complete this method

        $order = Order::firstWhere('external_order_id', $data['order_id']);

        if($order) {
            \Log::info("Commission: $order->commission_owed");
        }

        $user = User::with('affiliate')->where([
                'email' => $data['customer_email'],
                'type' => User::TYPE_AFFILIATE
            ])->first();

        if(!$user) {
            $user = User::create([
                'name' => $data['customer_name'],
                'email' => $data['customer_email'],
                'type' => User::TYPE_AFFILIATE
            ]);
        }

        $merchant = Merchant::firstWhere('domain', $data['merchant_domain']);

        if(!$merchant) {
            $merchant = $user->merchant()->create([
                'domain' => $data['merchant_domain'],
                'display_name' => $data['customer_name']
            ]);
        }

        try {
            $this->affiliateService->register($merchant, $data['customer_email'], $data['customer_name'], 0.1);
        } catch(\Exception $e) {}

        Order::firstOrCreate(
            [
              'external_order_id' => $data['order_id']
            ],
            [
            'subtotal' => $data['subtotal_price'],
          //  'affiliate_id' => Affiliate::first()->id,
            'merchant_id' => $merchant->id,
           // 'commission_owed' => $data['subtotal_price'] * Affiliate::first()->commission_rate
        ]);

        // Duplicate error test
        if(isset($data['customer_email'])) {
            $user = User::where('email', $data['customer_email'])->first();
            if(!$user) {
                $user = User::create([
                    'name' => $data['customer_name'],
                    'email' => $data['customer_email'],
                    'type' => User::TYPE_AFFILIATE
                ]);
            }

            $merchant = Merchant::firstWhere('domain', $data['merchant_domain']);

            if(!$merchant) {
                $merchant = $user->merchant()->create([
                    'domain' => $data['merchant_domain'],
                    'display_name' => $data['customer_name']
                ]);
            }

            Order::firstOrCreate([
                'external_order_id' => $data['order_id']
            ], [
                'merchant_id' => $merchant->id,
                'subtotal' => $data['subtotal_price'],
                'external_order_id' => $data['order_id']
            ]);
        }
    }
}
