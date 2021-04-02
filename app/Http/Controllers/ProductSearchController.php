<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Variant;
use Illuminate\Http\Request;

class ProductSearchController extends Controller
{
    public $default_pagination;

    public function __construct()
    {
        $this->default_pagination = config('essentials.default_pagination');
    }
    public function search(Request $request)
    {
        $pagination = $this->default_pagination;
        $query = Product::with(['variant_price'])
            ->where('title','LIKE',"%$request->title%");
        if ($request->has('date') && strlen($request->date) > 0){
            $query->orWhereDate('products.created_at',$request->date);
        }
        if ($request->has('variant') && strlen($request->variant) > 0)
        {
            $query->leftJoin('product_variants','product_variants.id','=','products.id')
                ->orWhere('product_variants.variant','LIKE',"%$request->variant%");
        }
        if ($request->has(['price_from','price_to']) && strlen($request->price_from) > 0 && strlen($request->price_to) > 0){

            $query->leftJoin('product_variant_prices','products.id','=','product_variant_prices.product_id')
                ->orWhere('product_variant_prices.price','>=',$request->price_from)
                ->orWhere('product_variant_prices.price','<=',$request->price_to);
        }
        $products = $query->paginate($pagination);
        $variants = Variant::with('product_variant')->get();
        return view('products.index',compact('products','variants'));

    }
}
