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

        // check affiliate if exist return other create
        $order = Order::firstWhere([
            'customer_email' => $data['customer_email']
        ]);

        if(is_null($order)) {
            Affiliate::create([
                'user_id' => 1,
                'merchant_id' => 1,
                'commission_rate' => 0.5,
                'discount_code' => $data['discount_code']
            ]);
        }
    }
}
