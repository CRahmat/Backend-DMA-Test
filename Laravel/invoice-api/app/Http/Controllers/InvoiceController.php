<?php

namespace App\Http\Controllers;

use App\Models\Invoice;
use App\Models\Product;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class InvoiceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $perPage = $request->input("per_page", 2);
        $date = $request->input("date", Carbon::today());

        //Get All Invoice
        $invoice = Invoice::whereDate('created_at', $date)
            ->orderByDesc('created_at')
            ->simplePaginate($perPage)
            ->items();

        $totalTransaksiTunai = 0;
        $totalTunaiPokok = 0;
        /**
         * @var \App\Models\Invoice $inv
         */
        foreach (Invoice::whereDate('created_at', $date)->get() as $inv) {
            /**
             * @var \App\Models\Product @product
             */
            foreach ($inv->list_products_sold as $product) {
                //Total Price Sold Dikalikan Quantity Karena Yang Masuk Adalah Harga Per Produk
                $totalTransaksiTunai += ($product['total_price_sold'] * $product['quantity']);
                $totalTunaiPokok += ($product['total_cost_of_goods_sold'] * $product['quantity']);
            }
        }

        $totalKeuntungan = $totalTransaksiTunai - $totalTunaiPokok;

        $response = [
            "data" => [
                "invoice" => $invoice,
            ],
            "total_transaksi_tunai" => $totalTransaksiTunai,
            "total_keuntungan" => $totalKeuntungan,
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
        $product = Product::all()->count();
        if($product > 0){
            try {
                $body = json_decode($request->getContent());
                $data = new Invoice();
                $rules = [
                    'invoice_no' => ['required', 'string', 'min:1'],
                    'date' => ['required', 'date'],
                    'customer_name' => ['required', 'string', 'min:2'],
                    'salesperson_name' => ['required', 'string', 'min:2'],
                    'payment_type' => ['required', 'in:cash,credit'],
                    'notes' => ['required', 'string', 'min:5'],
                    'list_products_sold' => ['required', 'array', 'min:1'],
                ];
    
                $validator = Validator::make((array) $body, $rules);
                if ($validator->fails()) {
                    return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
                }

                $data->invoice_no = $body->invoice_no;
                $data->date = $body->date;
                $data->customer_name = $body->customer_name;
                $data->salesperson_name = $body->salesperson_name;
                $data->payment_type = $body->payment_type;
                $data->notes = $body->notes;
                $data->list_products_sold = (array) $body->list_products_sold;

                $notFoundProduct = [];
                foreach($body->list_products_sold as $listProduct){
                    $product  = Product::where("id", (int)$listProduct->id)
                    ->count();
                    if($product == 0) {
                        array_push($notFoundProduct, $product);
                    }
                }

                if(count($notFoundProduct) > 0){
                    return self::falseResponse("Data Invoice Gagal Dibuat, Terdapat ".count($notFoundProduct)." Produk Tidak Terdaftar");
                }
                if ($data->save()) {
                    return self::trueResponse("Data Invoice Berhasil DiBuat");
                }
                return self::falseResponse("Data Invoice Gagal Dibuat", 500);
            } catch (\Exception $e) {
                return self::falseResponse($e->getMessage(), 500);
            }
        } else {
            return self::falseResponse("Data Invoice Gagal DiBuat, Belum Terdapat Produk Tersedia", 404);
        }
    }

    /**
     * OPTIONAL
     * Fungsi Ini Dapat Digunakan Jika Di Butuhkan.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function show($invoice_no)
    {
        try {
        $invoice = Product::where('invoice_no', $invoice_no)->first();
        if($invoice != null){
            $response = [
                'data' => $invoice
            ];
            return response()->json($response, Response::HTTP_OK);
        }
        return self::falseResponse("Data Invoice Tidak Ditemukan", 404);
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
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $invoice_no)
    {
        try {
            $invoice = Invoice::where('invoice_no', $invoice_no)->first();
            if($invoice != null){
                $body = json_decode($request->getContent());
                $data = $invoice;
                $rules = [
                    'invoice_no' => ['required', 'numeric', 'min:1'],
                    'date' => ['required', 'string'],
                    'customer_name' => ['required', 'string', 'min:2'],
                    'salesperson_name' => ['required', 'string', 'min:2'],
                    'payment_type' => ['required', 'in:cash,credit'],
                    'notes' => ['required', 'string', 'min:5'],
                    'list_products_sold' => ['required', 'array'],
                ];
    
                $validator = Validator::make((array) $body, $rules);
    
                if ($validator->fails()) {
                    return response()->json($validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
                }
                $data->invoice_no = $body->invoice_no;
                $data->date = $body->date;
                $data->customer_name = $body->customer_name;
                $data->salesperson_name = $body->salesperson_name;
                $data->payment_type = $body->payment_type;
                $data->notes = $body->notes;
                $data->list_products_sold = (array) $body->list_products_sold;
                    
                if ($invoice->update(array($data))) {
                    return self::trueResponse("Data Invoice Berhasil DiPerbaiki");
                }
            }
            return self::falseResponse("Data Invoice Tidak Ditemukan", 404);
        } catch (\Exception $e) {
            return response()->json([
                'message' => "Data Invoice Gagal Di Perbaiki" . $e->errorInfo,
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Invoice  $invoice
     * @return \Illuminate\Http\Response
     */
    public function destroy($invoice_no)
    {
        try {
            $invoice = Invoice::where('invoice_no', $invoice_no)->first();
            if($invoice != null) {
                $invoice->delete();
                return self::trueResponse("Data Invoice No ".$invoice_no." Berhasil DiHapus");
            }
            return self::falseResponse("Data Invoice Tidak Ditemukan", 404);
        } catch (Exception $e) {
            return response()->json([
                'message' => "Data Invoice Gagal Di Hapus" . $e->errorInfo,
            ]);
        }
    }
}
