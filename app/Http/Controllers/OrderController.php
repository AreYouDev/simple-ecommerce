<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $orders = \App\Models\Order::all();
        return view('userlayer.siparisler', compact('orders'));
    }

    public function show($id)
    {
        $order = \App\Models\Order::find($id);
        return view('userlayer.siparisdetay', compact('order'));
    }
}
