<?php

namespace App\Http\Controllers\Site;

use Cart;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Services\PayPalService;
use Illuminate\Http\Request;

class CheckoutController extends Controller
{
    protected $payPal;
    public function getCheckout()
    {
        return view('site.pages.checkout');
    }

    public function __construct(PayPalService $payPal)
    {
        $this->payPal = $payPal;
       // $this->orderRepository = $orderRepository;
    }

    public function placeOrder(Request $request)
    {
        // Before storing the order we should implement the
        // request validation which I leave it to you

        $order = Order::storeOrderDetails($request->all());
       // return $order;
        if ($order) {
           // return  $this->payPal;
          return  $this->payPal->processPayment($order);
        }


        return redirect()->back()->with('message','Order not placed');
    }
/*    public function storeOrderDetails($reqest)
    {
        $order = Order::create([
            'order_number'      =>  'ORD-'.strtoupper(uniqid()),
            'user_id'           => auth()->user()->id,
            'status'            =>  'pending',
            'grand_total'       =>  Cart::getSubTotal(),
            'item_count'        =>  Cart::getTotalQuantity(),
            'payment_status'    =>  0,
            'payment_method'    =>  null,
            'first_name'        =>  $reqest['first_name'],
            'last_name'         =>  $reqest['last_name'],
            'address'           =>  $reqest['address'],
            'city'              =>  $reqest['city'],
            'country'           =>  $reqest['country'],
            'post_code'         =>  $reqest['post_code'],
            'phone_number'      =>  $reqest['phone_number'],
            'notes'             =>  $reqest['notes']
        ]);

        if ($order) {

            $items = Cart::getContent();

            foreach ($items as $item)
            {
                // A better way will be to bring the product id with the cart items
                // you can explore the package documentation to send product id with the cart
                $product = Product::where('name', $item->name)->first();

                $orderItem = new OrderItem([
                    'product_id'    =>  $product->id,
                    'quantity'      =>  $item->quantity,
                    'price'         =>  $item->getPriceSum()
                ]);

                $order->items()->save($orderItem);
            }
        }

        return $order;
    }*/

    public function complete(Request $request)
    {
        $paymentId = $request->input('paymentId');
        $payerId = $request->input('PayerID');

        $status = $this->payPal->completePayment($paymentId, $payerId);

        $order = Order::where('order_number', $status['invoiceId'])->first();
        $order->status = 'processing';
        $order->payment_status = 1;
        $order->payment_method = 'PayPal -'.$status['salesId'];
        $order->save();

        Cart::clear();
        return view('site.pages.success', compact('order'));
    }
}
