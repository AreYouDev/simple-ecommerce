@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Dashboard') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif

                        {{ __('You are logged in!') }}

                        <hr>

                        <!-- Ürün Ekleme Formu -->
                        <form action="{{ route('products.store') }}" method="POST">
                            @csrf <!-- CSRF token eklemeyi unutmayın -->

                            <div class="form-group">
                                <label for="name">{{ __('Product Name') }}</label>
                                <input type="text" class="form-control" id="name" name="name" required>
                            </div>

                            <div class="form-group">
                                <label for="description">{{ __('Description') }}</label>
                                <textarea class="form-control" id="description" name="description" rows="3" required></textarea>
                            </div>

                            <div class="form-group">
                                <label for="price">{{ __('Price') }}</label>
                                <input type="text" class="form-control" id="price" name="price" required>
                            </div>

                            <div class="form-group">
                                <label for="stock">{{ __('Stock') }}</label>
                                <input type="number" class="form-control" id="stock" name="stock" required>
                            </div>

                            <button type="submit" class="btn btn-primary">{{ __('Add Product') }}</button>
                        </form>
                        <!-- Ürün Ekleme Formu Sonu -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
