@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Ödeme Ekranı Bilgi Giriş') }}</div>
                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="card-body">
                        <form action="{{route("odeme.sonuc")}}" method="POST" class="login100-form validate-form">
                            {{csrf_field()}}
                            <div class="wrap-input100 validate-input m-b-26"
                                 data-validate="Ad Soyad Alanı Zorunludur !">
                                <span class="label-input100">Ad Soyad</span>
                                <input class="input100" type="text" name="adsoyad" value="{{$userpaydata->name}}">
                                <span class="focus-input100"></span>
                            </div>
                            <div class="wrap-input100 validate-input m-b-26" data-validate="Telefon Alanı Zorunludur !">
                                <span class="label-input100">Telefon</span>
                                <input class="input100" type="text" name="telefon" value="{{$userpaydata->phone}}">
                                <span class="focus-input100"></span>
                            </div>
                            <div class="wrap-input100 validate-input m-b-26" data-validate="Miktar Alanı Zorunludur !">
                                <span class="label-input100">Miktar</span>
                                <input class="input100" type="number" name="miktar" value="1">
                                <span class="focus-input100"></span>
                            </div>

                            <select class="form-control " name="urun" required="" style="margin-bottom: 21px; height: 47px;">
                                <option value="">Hizmet Seçiniz.</option>
                                <option value="Grafik-tasarim">Grafik Tasarım</option>
                                <option value="web-tasarim">Web Tasarım</option>
                                <option value="teknik-destek">Teknik Destek</option>
                                <option value="Domain-Hosting">Domain + Hosting</option>
                            </select>


                            <div class="container-login100-form-btn">
                                <button type="submit" class="login100-form-btn">
                                    Ödeme Yap
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
