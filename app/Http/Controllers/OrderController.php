<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\Http\Resources\ProductCollection;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use Illuminate\Database\QueryException;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    /**
     * @param string $productsCacheKey
     */
    public function __construct(
        private readonly string $productsCacheKey = 'index_products_cache'
    ) {}

    /**
     * @param CreateOrderRequest $request
     * @return JsonResponse
     */
    public function createOrder(CreateOrderRequest $request): JsonResponse
    {
        $orderedItems = Arr::keyBy($request->get('items'), 'product_id');

        try {
            $orderSum = $this->newOrderTransaction($orderedItems);

            return response()->json(['ok' => true, 'total' => $orderSum]);
        } catch (QueryException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()]);
        }
    }

    /**
     * @return ProductCollection
     */
    public function getProducts(): ProductCollection
    {
        if (($products = Cache::get($this->productsCacheKey)) === null) {
            $products = Product::all();
            Cache::set($this->productsCacheKey, $products, 60 * 2); //two minutes
        }

        return new ProductCollection($products);
    }

    /**
     * @param array $orderedItems
     * @return string
     * @throws QueryException
     */
    private function newOrderTransaction(array $orderedItems): string
    {
        $orderSum = '0.00';

        DB::transaction(function () use ($orderedItems, &$orderSum) {
            $products = Product::query()->whereIn(
                'id',
                array_keys($orderedItems)
            )->pluck('price', 'id');

            $orderId = Order::query()->insertGetId([
                'user_id' => Auth::id(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $orderDetails = [];
            foreach ($products as $productId => $price) {
                $quantity = $orderedItems[$productId]['quantity'];

                $orderDetails[] = [
                    'order_id' => $orderId,
                    'product_id' => $productId,
                    'price' => $positionPrice = bcmul($price, $quantity, 2), //Using the bcmath library to obtain accurate calculations when working with float numbers
                    'quantity' => $quantity,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];

                $orderSum = bcadd($orderSum, $positionPrice, 2); //Using the bcmath library to obtain accurate calculations when working with float numbers
            }

            Order::query()->where('id', $orderId)->update([
                'order_sum' => $orderSum,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            OrderDetail::query()->insert($orderDetails);
        });

        return $orderSum;
    }
}
