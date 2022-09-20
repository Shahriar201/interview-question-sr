<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function index()
    {
        $products = DB::table('product_variants as pv')
                    ->join('products as p', 'pv.product_id', 'p.id')
                    ->join('variants as v', 'pv.variant_id', 'v.id')
                    ->join('product_variant_prices as pvp', 'pv.product_id', 'pvp.product_id')
                    ->select('p.*')
                    // ->where('pv.id', 'pvp.product_variant_one')
                    // ->where('pv.id', 'pvp.product_variant_two')
                    // ->where('pv.id', 'pvp.product_variant_three')
                    ->groupBy('p.id')
                    ->paginate(5);

        $variantSize = DB::table('product_variants as pv')
                    ->join('product_variant_prices as pvp', 'pv.product_id', 'pvp.product_id')
                    ->join('variants as v', 'pv.variant_id', 'v.id')
                    ->select('pv.variant', 'pvp.stock')
                    ->where('v.id', 2)
                    ->groupBy('pv.variant')
                    ->paginate(5);
        $variantColor = DB::table('product_variants as pv')
                    ->join('product_variant_prices as pvp', 'pv.product_id', 'pvp.product_id')
                    ->join('variants as v', 'pv.variant_id', 'v.id')
                    ->select('pv.variant', 'pvp.stock')
                    ->where('v.id', 1)
                    ->groupBy('pv.variant')
                    ->paginate(5);
        $variantStyle = DB::table('product_variants as pv')
                    ->join('product_variant_prices as pvp', 'pv.product_id', 'pvp.product_id')
                    ->join('variants as v', 'pv.variant_id', 'v.id')
                    ->select('pv.variant', 'pvp.stock')
                    ->where('v.id', 6)
                    ->groupBy('pv.variant')
                    ->paginate(5);

        // dd($variantStyle->toArray());
        // dd($variantColor->toArray());
        // dd($variantSize->toArray());
        // dd($products->toArray());

        return view('products.index', compact('products', 'variantSize', 'variantColor', 'variantStyle'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function create()
    {
        $variants = Variant::all();
        return view('products.create', compact('variants'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request)
    {

    }


    /**
     * Display the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function show($product)
    {

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function edit(Product $product)
    {
        $variants = Variant::all();
        return view('products.edit', compact('variants'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        //
    }

    public function productFilter(Request $request) {
        // dd($request->all());
        if(isset($request->date)) {
            $products = DB::table('products')
                ->where('title', 'like', $request->title.'%')
                ->orWhere('pvp.price', '>=', 'price_from')
                ->orWhere('pvp.price', '<=', 'price_to')
                ->where(function ($q) use ($request) {
                    $q->whereDate('created_at', '=', $request->date);
                })
                ->paginate(5);
        } else {
            $products = DB::table('products')
                ->join('product_variant_prices as pvp', 'products.id', 'pvp.product_id')
                ->where('title', 'like', $request->title.'%')
                ->where('pvp.price', '>=', 'price_from')
                ->where('pvp.price', '<=', 'price_to')
                ->groupBy('pvp.product_id')
                ->paginate(5);
        }

                // dd($products);

        return view('products.index', compact('products'));
    }
}
