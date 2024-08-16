@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Shopping Cart</h1>
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        @if(isset($cart) && $cart->products->count() > 0)
            <table class="table">
                <thead>
                <tr>
                    <th>Product</th>
                    <th>Price</th>
                    <th>Quantity</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @foreach($cart->products as $product)
                    <tr>
                        <td>{{ $product->name }}</td>
                        <td>${{ number_format($product->price, 2) }}</td>
                        <td>{{ $product->pivot->quantity }}</td>
                        <td>${{ number_format($product->price * $product->pivot->quantity, 2) }}</td>
                        <td>
                            <form action="{{ route('cart.remove', $product->id) }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            <div class="text-right">
                <form action="{{ route('cart.complete') }}" method="POST">
                    @csrf
                    <button type="submit">Complete Checkout</button>
                </form>
            </div>
        @else
            <p>Your cart is empty.</p>
        @endif
    </div>
@endsection
