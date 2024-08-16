@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Profile Page') }}</div>
                    @if(session('success'))
                        <div class="alert alert-success" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif
                    <div class="card-body">
                        <h2>Profil Bilgileri</h2>
                        <form method="post" action="{{route('profile.update')}}">
                            @csrf
                            <!-- Ad -->
                            <div class="mb-3">
                                <label for="userName" class="form-label">Adı</label>
                                <input type="text" class="form-control" disabled id="userName" placeholder="{{$userdata->name}}">
                            </div>

                            <!-- Telefon Numarası -->
                            <div class="mb-3">
                                <label for="userPhone" class="form-label">Telefon Numarası</label>
                                <input type="tel" class="form-control" id="userPhone" disabled placeholder="{{$userdata->phone}}">
                            </div>

                            <!-- Eposta -->
                            <div class="mb-3">
                                <label for="userEmail" class="form-label">Eposta</label>
                                <input type="email" class="form-control" id="userEmail" disabled placeholder="{{$userdata->email}}">
                            </div>

                            <!-- Şifre -->
                            <div class="mb-3">
                                <label for="userPassword" class="form-label">Yeni Şifre</label>
                                <input type="password" class="form-control" id="userPassword" name="newpassword" placeholder="Yeni Şifreniz">
                            </div>

                            <button type="submit" class="btn btn-primary">Bilgileri Güncelle (Opsyoneldir)</button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </div>
@endsection
