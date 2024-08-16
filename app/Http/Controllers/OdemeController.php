<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderProduct;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OdemeController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    public function index(){
        $userpaydata = Auth::user();
        return view("odeme.index", ['userpaydata' => $userpaydata]);
    }
    public function odeme(){
        $user = Auth::user();
        $cart = Cart::with('products')->where('user_id', $user->id)->where('completed', false)->firstOrFail();

        // Sepetteki ürünlerin toplam tutarını hesapla
        $payment_amount = 0;
        foreach ($cart->products as $product) {
            $payment_amount += $product->price * $product->pivot->quantity;
        }
        $payment_amount *= 100; // PayTR formatına çevir (Kuruş cinsinden)
        $udata = Auth::user();
        $data =request()->all();
        ## 1. ADIM için örnek kodlar ##

        ####################### DÜZENLEMESİ ZORUNLU ALANLAR #######################
        #
        ## API Entegrasyon Bilgileri - Mağaza paneline giriş yaparak BİLGİ sayfasından alabilirsiniz.
        $merchant_id    = env('PAYTR_MERCANT_ID');
        $merchant_key   = env('PAYTR_MERCANT_KEY');
        $merchant_salt  = env('PAYTR_MERCANT_SALT');

        ###########################################################################

        $random_id = "11".rand(1,999).rand(1,88)*rand(1,50);
        $email = $udata->email;
        $merchant_oid = $random_id;
        $user_name = $user->name;
        $user_address = $user->address;
        $user_phone = $user->phone;
        ## Başarılı ödeme sonrası müşterinizin yönlendirileceği sayfa
        ## !!! Bu sayfa siparişi onaylayacağınız sayfa değildir! Yalnızca müşterinizi bilgilendireceğiniz sayfadır!
        ## !!! Siparişi onaylayacağız sayfa "Bildirim URL" sayfasıdır (Bakınız: 2.ADIM Klasörü).
        $merchant_ok_url = "https://oyunum.store/basarili";
        ## Ödeme sürecinde beklenmedik bir hata oluşması durumunda müşterinizin yönlendirileceği sayfa
        ## !!! Bu sayfa siparişi iptal edeceğiniz sayfa değildir! Yalnızca müşterinizi bilgilendireceğiniz sayfadır!
        ## !!! Siparişi iptal edeceğiniz sayfa "Bildirim URL" sayfasıdır (Bakınız: 2.ADIM Klasörü).
        $merchant_fail_url = "https://oyunum.store/basarisiz";


        /*
        $user_basket = base64_encode(json_encode($cart->products->map(function ($product) {
            return [
                $product->name, // Ürün adı
                $product->price * 100, // Fiyat (kuruş olarak)
                $product->pivot->quantity // Miktar
            ];
        })->toArray()));

        $total_amount = $product->price * $product->pivot->quantity;
        $payment_amount = $total_amount*100; // Toplam tutar (kuruş cinsinden)*/

        $totalPrice = 0; // Toplam fiyatı saklamak için değişken
        $userBasket = []; // Sepetteki ürünler için dizi

        foreach ($cart->products as $product) {
            // Her ürün için toplam fiyata ürün fiyatını ve miktarını ekle
            $totalPrice += $product->price * $product->pivot->quantity;

            // user_basket alanını her bir ürün için güncelle
            $userBasket[] = [
                $product->name, // Ürün adı
                (string) number_format($product->price, 2, '.', ''), // Birim fiyat, TL cinsinden ve string olarak
                (string) $product->pivot->quantity, // Miktar, string olarak
            ];
        }


        // user_basket'ı JSON string olarak kodla ve base64 ile kodla
        $userBasketJson = base64_encode(json_encode($userBasket));

        ## Kullanıcının IP adresi
        if( isset( $_SERVER["HTTP_CLIENT_IP"] ) ) {
            $ip = $_SERVER["HTTP_CLIENT_IP"];
        } elseif( isset( $_SERVER["HTTP_X_FORWARDED_FOR"] ) ) {
            $ip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        } else {
            $ip = $_SERVER["REMOTE_ADDR"];
        }

        ## !!! Eğer bu örnek kodu sunucuda değil local makinanızda çalıştırıyorsanız
        ## buraya dış ip adresinizi (https://www.whatismyip.com/) yazmalısınız. Aksi halde geçersiz paytr_token hatası alırsınız.
        #$user_ip=$ip;
        $user_ip="195.33.208.242";

        ## İşlem zaman aşımı süresi - dakika cinsinden
        $timeout_limit = "5";

        ## Hata mesajlarının ekrana basılması için entegrasyon ve test sürecinde 1 olarak bırakın. Daha sonra 0 yapabilirsiniz.
        $debug_on = 1;

        ## Mağaza canlı modda iken test işlem yapmak için 1 olarak gönderilebilir.
        $test_mode = 0;

        $no_installment = 1; // Taksit yapılmasını istemiyorsanız, sadece tek çekim sunacaksanız 1 yapın

        ## Sayfada görüntülenecek taksit adedini sınırlamak istiyorsanız uygun şekilde değiştirin.
        ## Sıfır (0) gönderilmesi durumunda yürürlükteki en fazla izin verilen taksit geçerli olur.
        $max_installment = 0;

        $currency = "TL";
        // Toplam fiyatı kuruş cinsine çevir
        $paymentAmount = $totalPrice * 100;

        ####### Bu kısımda herhangi bir değişiklik yapmanıza gerek yoktur. #######
        $hash_str = $merchant_id .$user_ip .$merchant_oid .$email .$payment_amount .$userBasketJson.$no_installment.$max_installment.$currency.$test_mode;
        $paytr_token=base64_encode(hash_hmac('sha256',$hash_str.$merchant_salt,$merchant_key,true));
        $data_vals=array(
            'merchant_id'=>$merchant_id,
            'user_ip'=>$user_ip,
            'merchant_oid'=>$merchant_oid,
            'email'=>$email,
            'payment_amount'=>$payment_amount,
            'paytr_token'=>$paytr_token,
            'user_basket'=>$userBasketJson,
            'debug_on'=>$debug_on,
            'no_installment'=>$no_installment,
            'max_installment'=>$max_installment,
            'user_name'=>$user_name,
            'user_address'=>$user_address,
            'user_phone'=>$user_phone,
            'merchant_ok_url'=>$merchant_ok_url,
            'merchant_fail_url'=>$merchant_fail_url,
            'timeout_limit'=>$timeout_limit,
            'currency'=>$currency,
            'test_mode'=>$test_mode
        );

        $ch=curl_init();
        curl_setopt($ch, CURLOPT_URL, "https://www.paytr.com/odeme/api/get-token");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1) ;
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_vals);
        curl_setopt($ch, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);

        // XXX: DİKKAT: lokal makinanızda "SSL certificate problem: unable to get local issuer certificate" uyarısı alırsanız eğer
        // aşağıdaki kodu açıp deneyebilirsiniz. ANCAK, güvenlik nedeniyle sunucunuzda (gerçek ortamınızda) bu kodun kapalı kalması çok önemlidir!
        // curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);

        $result = @curl_exec($ch);

        if(curl_errno($ch))
            die("PAYTR IFRAME connection error. err:".curl_error($ch));

        curl_close($ch);

        $result=json_decode($result,1);

        if($result['status']=='success')
            $token=$result['token'];
        else
            die("PAYTR IFRAME failed. reason:".$result['reason']);

        $createorder = new Order();
        $createorder->user_id = $user->id;
        $createorder->paytr_id = $merchant_oid;
        $createorder->total_price = $totalPrice; // Burada $totalPrice, tüm ürünlerin toplam fiyatı olmalı
        $createorder->status = "pending";
        $createorder->save();

        foreach ($cart->products as $product) {
            $productTotalPrice = $product->price * $product->pivot->quantity; // Ürünün toplam fiyatını hesapla

            $neworderedProducts = new OrderProduct();
            $neworderedProducts->order_id = $createorder->id;
            $neworderedProducts->product_id = $product->id;
            $neworderedProducts->quantity = $product->pivot->quantity;
            $neworderedProducts->price = $product->price; // Ürünün birim fiyatı
            $neworderedProducts->total_price = $productTotalPrice; // Ürünün toplam fiyatı
            $neworderedProducts->status = "pending";
            $neworderedProducts->save();
        }

        return view("odeme.sonuc",compact("token"));
    }
    public function bildirim(){
        $data = request()->all();
        ####################### DÜZENLEMESİ ZORUNLU ALANLAR #######################
        $merchant_key   = env('PAYTR_MERCANT_KEY');
        $merchant_salt  = env('PAYTR_MERCANT_SALT');
        ###########################################################################
        ####### Bu kısımda herhangi bir değişiklik yapmanıza gerek yoktur. #######
        ## POST değerleri ile hash oluştur.
        $hash = base64_encode( hash_hmac('sha256', $data['merchant_oid'].$merchant_salt.$data['status'].$data['total_amount'], $merchant_key, true) );
        ## Oluşturulan hash'i, paytr'dan gelen post içindeki hash ile karşılaştır (isteğin paytr'dan geldiğine ve değişmediğine emin olmak için)
        ## Bu işlemi yapmazsanız maddi zarara uğramanız olasıdır.
        if( $hash != $data['hash'] )
            die('PAYTR notification failed: bad hash');
        ###########################################################################

        /* Sipariş durum sorgulama örnek */
        $order = Order::where('paytr_id', $data['merchant_oid'])->first();
        if( $order && $order->status != "pending" ) {
            echo "OK";
            exit;
        }

        if( $data['status'] == 'success' ) {
            ## Ödeme Onaylandı
            $updateorder = Order::where('paytr_id', $data['merchant_oid'])->first();
            $updateorder->status = "Onaylandı";
            $updateorder->save();
            redirect()->route('orders.index')->with("success", "Ödeme işlemi başarıyla tamamlandı.");
        } else {
            ## Ödemeye Onay Verilmedi
            $updateorder = Order::where('paytr_id', $data['merchant_oid'])->first();
            $updateorder->status = "Onaylanmadı";
            $updateorder->save();
            redirect()->route('odeme.sonuc')->with("error", "Ödeme işlemi başarısız oldu. Hata Kodu: ", $data['failed_reason_code'], $data['failed_reason_msg'], ". Lütfen tekrar deneyin.");
        }
        ## Bildirimin alındığını PayTR sistemine bildir.
        echo "OK";
        exit;
    }
}
