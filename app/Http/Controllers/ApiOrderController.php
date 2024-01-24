<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiOrderController extends Controller
{
    public function filter(Request $request) {
        $orders_filter = Order::with('user')->get();

        if (auth()->user()->role != 'admin') {
            $orders_filter = auth()->user()->orders;
        }

        // Apply filters
        if ($request->filterOrderName)
            $orders_filter = $orders_filter->where('title', $request->filterOrderName);
        if ($request->filterOrderCustomer)
            $orders_filter = $orders_filter->where('user.first_name', $request->filterOrderCustomer);
        if ($request->filterOrderPriceMin && $request->filterOrderPriceMax)
            $orders_filter = $orders_filter->whereBetween('total_price', [$request->filterOrderPriceMin, $request->filterOrderPriceMax]);

        // Convert the filtered orders to JSON
        $filteredOrders = $orders_filter->toJson();

        // Return the JSON response
        return response()->json(['orders' => json_decode($filteredOrders)]);
    }
    public function index()
    {
        $orders = Order::orderby('id')->get();

        return response()->json(['orders' => $orders]);
    }

    public function create()
    {
        $users = User::where('status', 'enable')->get();
        $products = Product::where('status', 'enable')->get();

        return response()->json(['users' => $users, 'products' => $products]);
    }

    public function store(Request $request)
    {
        $total_price = 0;
        $products = Product::where('status', 'enable')->get();
        foreach ($request->products as $product) {
            $total_price += ($product['price']) * ($product['count']);
        }
        Order::Create([
            'user_id' => $request->user_id,
            'title' => $request->order_name,
            'price' => $total_price,
            'created_at' => date('Y-m-d H:i:s'),
        ]);
        $order_id = Order::orderby('id', 'desc')->first();

        foreach ($products as $product) {
            $product_name = 'product_' . $product->id;
            if ($request->$product_name) {

                $product->orders()->save($order_id, [
                    'count' => $request->$product_name,
                ]);
            }
            return response()->json(['message' => 'Order created successfully']);
        }
    }

    public function update(UpdateOrderRequest $request, $id)
    {
        $order = Order::where('id', $id)->first();

        if ($order->user->id == auth('api')->user()->id) {
            $total_price = 0;
            $order_products = DB::table('orders')->join('order_product', 'order_product.order_id', '=', 'orders.id')->where('orders.id', $order->id)
                ->get();
            foreach (Product::all() as $product) {
                if (in_array($product->id, collect($request->products)->pluck('id')->toArray())) {
                    $api_count = collect($request->products)->where('id', $product->id)->first()['count'];
                    $api_price = Product::find($product->id)->price;
                    $total_price += $api_count * $api_price;
                    if ($order_products->where('product_id', $product->id)->count())
                        $database_count = $order_products->where('product_id', $product->id)->first()->count;
                    else
                        $database_count = 0;
                } elseif ($order_products->where('product_id', $product->id)->count()) {
                    $api_count = 0;
                    $database_count = $order_products->where('product_id', $product->id)->first()->count;
                    $database_price = Product::find($product->id)->price;
                    $total_price += $database_count * $database_price;
                } else {
                    $database_count = $api_count = 0;
                }
            }
            $newinventory = Product::find($product->id)->inventory + $database_count - $api_count;
            Product::find($product->id)->update([
                'inventory' => $newinventory,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
        $order->update([
            'title' => $request->order_name,
//            'user_id' => auth('api')->user()->id,
        'user_id' => $request->user_id,
            'total_price' => $total_price,
            'updated_at' => date('Y-m-d H:i:s'),
        ]);

        $order->products()->detach();
        foreach (Product::all() as $product) {
            if (in_array($product->id, collect($request->products)->pluck('id')->toArray())) {
                if (collect($request->products)->where('id', $product->id)->first()['count']) {
                    $product->orders()->save($order,
                        [
                            'count' => collect($request->products)->where('id', $product->id)->first()['count'],
                            'updated_at' => date('Y-m-d H:i:s'),
                        ]);
                }
            } elseif ($order_products->where('product_id', $product->id)->count()) {
                $product->orders()->save($order, ['count' => $order_products->where('product_id', $product->id)->first()->count,
                    'updated_at' => date('Y-m-d H:i:s'),
                ]);
            }
            return response()->json([
                'status' => false,
                'message' => 'you couldnt acces'
            ]);
        }
        return response()->json([
            'status' => true,
            'message' => 'order updated successfuly'
        ]);
    }

    public function destroy($id)
    {
        $orders = Order::findOrFail($id);
        $orders->delete();
        return response()->json(['message' => 'Order deleted successfully']);
    }
}
