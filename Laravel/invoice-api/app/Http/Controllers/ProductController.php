<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Get All Products
        $products = Product::OrderBy('created_at', 'DESC')->get();
        $response = [
            "data" => $products
        ];

        return response()->json($response, Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $body = json_decode($request->getContent());
            $data = new Product();
            $rules = [
                'product_name'=> ['required', 'string', 'min:5'],
                'quantity' => ['required', 'numeric', 'min:1'],
                'total_cost_of_goods_sold' => ['required', 'numeric', 'min:0'],
                'total_price_sold' => ['required', 'numeric', 'min:0']
            ];

            $validator = Validator::make((array) $body, $rules);
    
            if ($validator->fails()) {
                return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $data->product_name = $body->product_name;
            $data->quantity = $body->quantity;
            $data->total_cost_of_goods_sold = $body->total_cost_of_goods_sold;
            $data->total_price_sold = $body->total_price_sold;
            if ($data->save()) {
                return self::trueResponse("Data Produk Berhasil DiBuat");
            }
            return self::falseResponse("Data Produk Gagal Dibuat", 500);
        }catch(Exception $e){
            return response()->json([
                'message' => "Product Gagal " . $e->errorInfo
            ]);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
        $product = Product::where('id', $id)->first();
        if($product != null){
            $response = [
                'data' => $product
            ];
            return response()->json($response, Response::HTTP_OK);
        }
        return self::falseResponse("Data Produk Tidak Ditemukan", 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Error" . $e->errorInfo,
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $product = Product::where('id', $id)->first();
            if($product != null){
                $body = json_decode($request->getContent());
                $data = $product;
                $rules = [
                    'product_name'=> ['required', 'string', 'min:5'],
                    'quantity' => ['required', 'numeric', 'min:1'],
                    'total_cost_of_goods_sold' => ['required', 'numeric', 'min:0'],
                    'total_price_sold' => ['required', 'numeric', 'min:0']
                ];
    
                $validator = Validator::make((array) $body, $rules);
    
                if ($validator->fails()) {
                    return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                $data->product_name = $body->product_name;
                $data->quantity = $body->quantity;
                $data->total_cost_of_goods_sold = $body->total_cost_of_goods_sold;
                $data->total_price_sold = $body->total_price_sold;
                    
                if ($product->update(array($data))) {
                    return self::trueResponse("Data Produk Berhasil DiPerbaiki");
                }
            }
            return self::falseResponse("Data Produk Tidak Ditemukan", 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Data Produk Gagal Di Perbaiki" . $e->errorInfo,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Product  $product
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $product = Product::find($id);
            if($product != null) {
                $product->delete();
                return self::trueResponse("Data Produk No ".$id." Berhasil DiHapus");
            }
            return self::falseResponse("Data Produk Tidak Ditemukan", 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Data Produk Gagal Di Hapus" . $e->errorInfo,
            ]);
        }
    }
}
