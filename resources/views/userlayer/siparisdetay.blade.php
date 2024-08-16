{{-- resources/views/orders/show.blade.php --}}
@extends('layouts.app')

@section('content')
    <div class="container">
        <h2>Sipariş Detayları</h2>
        <div class="card">
            <div class="card-header">
                Sipariş #{{ $order->id }} - Ödeme ID: {{$order->paytr_id}} - Sipariş Tarihi: <span>{{ $order->created_at->format('d/m/Y') }}</span>
            </div>
            <div class="card-body">
                <h5 class="card-title">Toplam Tutar: {{ number_format($order->total_price, 2) }} TL</h5>
                <h6 class="card-subtitle mb-2 text-muted">Durum: {{ $order->status }}</h6>

                <table class="table mt-4">
                    <thead>
                    <tr>
                        <th>Ürün Adı</th>
                        <th>Miktar</th>
                        <th>Birim Fiyat</th>
                        <th>Toplam</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($order->products as $product)
                        <tr>
                            <td>{{ $product->name }}</td>
                            <td>{{ $product->pivot->quantity }}</td>
                            <td>{{ number_format($product->price, 2) }} TL</td>
                            <td>{{ number_format($product->price * $product->pivot->quantity, 2) }} TL</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="card-footer text-muted">
                <a href="{{ route('orders.index') }}" class="btn btn-primary">Tüm Siparişlerime Dön</a>
            </div>
        </div>
    </div>
@endsection
