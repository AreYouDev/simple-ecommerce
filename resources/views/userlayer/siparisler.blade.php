@extends('layouts.app')
@section('content')
    <div class="container">
        <h2>Benim Siparişlerim</h2>
        <table class="table">
            <thead>
            <tr>
                <th>Sipariş Numarası</th>
                <th>Sipariş Tarihi</th>
                <th>Toplam Tutar</th>
                <th>Durum</th>
                <th>Detay</th>
            </tr>
            </thead>
            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>
                    <td>{{ $order->created_at->format('d/m/Y') }}</td>
                    <td>{{ number_format($order->total_price, 2) }} TL</td>
                    <td>{{ $order->status }}</td>
                    <td>
                        <a href="{{ route('orders.show', $order->id) }}" class="btn btn-info btn-sm">Detaylar</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Sipariş bulunamadı.</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
@endsection
