<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use PayTR\Paytr;
use PayTR\Config;
use Exception;

class CartController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $cart = Cart::with('products')->where('user_id', Auth::id())->where('completed', false)->first();

        return view('adminlayer.cart', compact('cart'));
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $cart = Cart::firstOrCreate([
            'user_id' => Auth::id(),
            'completed' => false
        ]);

        // Ürün sepette var mı kontrol et
        $existingProduct = $cart->products()->where('product_id', $request->product_id)->first();

        if ($existingProduct) {
            // Ürün zaten sepette varsa, mevcut miktarı güncelle
            $existingQuantity = $cart->products()->find($request->product_id)->pivot->quantity;
            $cart->products()->updateExistingPivot($request->product_id, ['quantity' => $existingQuantity + $request->quantity]);
        } else {
            // Ürün sepette yoksa, yeni ürün olarak ekle
            $cart->products()->attach($request->product_id, ['quantity' => $request->quantity]);
        }

        return back()->with('success', 'Product added to cart successfully.');
    }

    public function remove($productId)
    {
        $cart = Cart::where('user_id', Auth::id())->where('completed', false)->first();

        if ($cart) {
            $cart->products()->detach($productId);
            return back()->with('success', 'Product removed from cart successfully.');
        }

        return back()->with('error', 'Cart not found.');
    }


    // Sepeti tamamlama (Checkout işlemi)
    public function complete(Request $request)
    {
        $user = Auth::user();
        $cartItems = Cart::where('user_id', $user->id)->with('products')->firstOrFail();

        // Örnek bir Config sınıfı yapılandırması varsayıyoruz
        $config = new Config();
        $config->setMerchantId(env('PAYTR_MERCHANT_ID'))
            ->setMerchantKey(env('PAYTR_MERCHANT_KEY'))
            ->setMerchantSalt(env('PAYTR_MERCHANT_SALT'))
            ->setApiUrl(env('PAYTR_API_URL') . 'odeme/api/get-token'); // env fonksiyonu kullanımı

        $orderid = Str::random(7);
        $totalprice = 0;
        foreach ($cartItems->products as $product) {
            $totalprice += $product->price * $product->pivot->quantity;
        }
        $totalprice = $totalprice * 100; // Total price in kuruş

        $userBasket = $cartItems->products->map(function ($product) {
            return [
                $product->name,
                number_format($product->price, 2, '.', ''),
                $product->pivot->quantity,
            ];
        })->toArray();

        // Hash data preparation
        $hash_data = [
            "merchant_id" => $config->getMerchantId(),
            "user_ip" => request()->ip(),
            "merchant_oid" => $orderid,
            "email" => $user->email,
            "payment_amount" => $totalprice,
            "user_basket" => base64_encode(json_encode($userBasket)),
            "no_installment" => $config->getNoInstallment(),
            "max_installment" => $config->getMaxInstallment(),
            "currency" => $config->getCurrency(),
            "test_mode" => $config->getTestMode(),
        ];

        $paytr = new Paytr($config);
        $post_data = [
            'user_name'         => $user->name,
            'user_address'      => "test",
            'user_phone'        => $user->phone, //Need [+][country code][area code][phone number]
            'paytr_token'       => $paytr->setHashStr($hash_data)->getToken()->token,
            'debug_on'          => $config->getDebugOn(),
            'merchant_ok_url'   => $config->getMerchantOkUrl(),
            'merchant_fail_url' => $config->getMerchantFailUrl(),
            'timeout_limit'     => $config->getTimeoutLimit()
        ];
        $post_data['paytr_token'] = $paytr->setHashStr($hash_data)->getToken()->token;

        $post_data = array_merge($post_data, $hash_data);

        // API çağrısını tetikle
        $trigger = $paytr->call($post_data, $config->getApiUrl());

        // $trigger sonucuna göre işlem yap
    }
}
