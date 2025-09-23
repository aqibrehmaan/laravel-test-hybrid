<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Merchant;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class MerchantController extends Controller
{
    public function __construct(
        MerchantService $merchantService
    ) {}

    /**
     * Useful order statistics for the merchant API.
     *
     * @param Request $request Will include a from and to date
     * @return JsonResponse Should be in the form {count: total number of orders in range, commission_owed: amount of unpaid commissions for orders with an affiliate, revenue: sum order subtotals}
     */
    public function orderStats(Request $request): JsonResponse
    {
        // TODO: Complete this method

        $from = $request->from;
        $to = $request->to;

        $orders = Order::whereBetween('created_at', [$from, $to]);
        $count = $orders->count();
        $subtotal = $orders->sum('subtotal');

        // $unpaidCommission = $orders->whereNotNull('affiliate_id')->where('commission_owed', '>', 0)->sum('commission_owed');

        $unpaidCommission = $orders->whereNotNull('affiliate_id')->sum('commission_owed') - $orders->whereNull('affiliate_id')->sum('commission_owed');

        return response()->json([
            'count' => $count,
            'commissions_owed' => $unpaidCommission,
            'revenue' => $subtotal
        ]);
    }
}
