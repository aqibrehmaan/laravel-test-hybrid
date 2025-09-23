<?php

namespace App\Http\Controllers;

use App\Services\AffiliateService;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class WebhookController extends Controller
{
    public function __construct(
        protected OrderService $orderService
    ) {}

    /**
     * Pass the necessary data to the process order method
     *
     * @param  Request $request
     * @return JsonResponse
     */
    public function __invoke(Request $request): JsonResponse
    {
        // TODO: Complete this method

        // $data = [
        //     "order_id" => "76b25715-a3e3-3cf5-b5e7-22fea30f7466",
        //     "subtotal_price" => 328.0,
        //     "merchant_domain" => "rau.com",
        //     "discount_code" => "c83ae17c-3ed9-3d7d-ad9e-b1b5dd1d5f99",
        // ];

        $data = [
            "order_id" => $request->order_id,
            "subtotal_price" => $request->subtotal_price,
            "merchant_domain" => $request->merchant_domain,
            "discount_code" => $request->discount_code,
        ];

        $this->orderService->processOrder($data);

        return response()->json([
            'status' => 200
        ]);
    }
}
