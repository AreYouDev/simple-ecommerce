<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $products = \App\Models\Product::all();
        return view('adminlayer.products', compact('products'));
    }

    public function create()
    {
        return view('adminlayer.addproduct');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'price' => 'required',
            'description' => 'required',
        ]);

        \App\Models\Product::create($request->all());

        return redirect()->route('products.index')->with('success', 'Product created successfully.');
    }
}
