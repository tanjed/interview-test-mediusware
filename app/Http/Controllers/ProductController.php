<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\ProductVariantPrice;
use App\Models\Variant;
use Carbon\Carbon;
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
        return view('products.index');
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
        //Validation will be places here
        $now = Carbon::now();
        DB::beginTransaction();
        try {
            $product = Product::create([
                'title' => ucfirst($request->title),
                'sku' => $request->sku,
                'description' => $request->description,
            ]);
            foreach ($request->product_image as $path)
            {
                ProductImage::create([
                    'product_id' => $product->id,
                    'file_path' => $path
                ]);
            }
            $prepared_variant_price_data = [];
            foreach ($request->product_variant_prices as $variant_price)
            {
                $variant_id = $variant_price['variant_id'];
                $product_variant = ProductVariant::create([
                    'variant'=> $variant_price['title'],
                    'variant_id' => $variant_id,
                    'product_id'=> $product->id,
                    'created_at' => $now->toDateTimeString(),
                    'updated_at' => $now->toDateTimeString()
                ]);
                array_push($prepared_variant_price_data,[
                    'product_variant_one' => $product_variant->id,
                    'price' => $variant_price['price'],
                    'stock' => $variant_price['stock'],
                    'product_id'=> $product->id,
                    'created_at' => $now->toDateTimeString(),
                    'updated_at' => $now->toDateTimeString()

                ]);
            }
            ProductVariantPrice::insert($prepared_variant_price_data);
            DB::commit();
            return $this->sendSuccessResponse(
                [
                    "product" => [
                        "id" => $product->id,
                        "title" => $product->title
                    ]
                ],'Product created.');
        }catch (\Exception $exception){
            DB::rollBack();
            return $this->sendErrorResponse($exception->getMessage());
        }
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

    public function uploadImage(Request $request)
    {
        $file = $request->file('file');
        $file_name = 'PI_'.round(microtime(true) * 1000);
        $path = $this->upload($file,'/product_images',$file_name);
        return $this->sendSuccessResponse([
            'path' => $path,
        ],'Upload success.');
    }
}
