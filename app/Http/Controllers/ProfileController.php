<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $userdata = Auth::user();
        return view('userlayer.profile', ['userdata' => $userdata]);
    }

    public function update(Request $request)
    {
        $userdata = Auth::user();
        $userdata->password = $request->newpassword;
        $userdata->save();
        return redirect()->route('profile')->with('success', 'Şifreniz Başarıyla Güncellendi.');
    }
}
