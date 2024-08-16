@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Ödeme Ekranı') }}</div>
                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="card-body">
                        <!-- Ödeme formunun açılması için gereken HTML kodlar / Başlangıç -->
                        <script src="https://www.paytr.com/js/iframeResizer.min.js"></script>
                        <iframe src="https://www.paytr.com/odeme/guvenli/{{$token}}" id="paytriframe" frameborder="0" scrolling="no" style="width: 100%;"></iframe>
                        <script>iFrameResize({},'#paytriframe');</script>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
