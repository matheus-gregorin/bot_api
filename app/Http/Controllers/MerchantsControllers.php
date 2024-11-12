<?php

namespace App\Http\Controllers;

use App\Services\MerchantsServices;
use Exception;
use Illuminate\Http\Request;

class MerchantsControllers extends Controller
{
    private MerchantsServices $merchantsServices;

    public function __construct(MerchantsServices $merchantsServices) {
        $this->merchantsServices = $merchantsServices;
    }

    public function create(Request $request)
    {
        try{

            $merchant = $this->merchantsServices->create($request->all());

            return response()->json([
                'success' => true,
                'merchant' => $merchant
            ]);
        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updated(string $uuid, Request $request)
    {
        try{

            $merchant = $this->merchantsServices->updated($uuid, $request->all());

            return response()->json([
                'success' => true,
                'merchant' => $merchant
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function deleted(string $uuid, Request $request)
    {
        try{

            $this->merchantsServices->deleted($uuid, $request->all());

            return response()->json([
                'success' => true,
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function get(string $uuid, Request $request)
    {
        try{

            $merchant = $this->merchantsServices->get($uuid, $request->all());

            return response()->json([
                'success' => true,
                'merchant' => $merchant
            ]);
        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function all(Request $request)
    {
        try{

            $merchants = $this->merchantsServices->all($request->all());
            $total = $merchants['total'] ?? 0;
            unset($merchants['total']);

            return response()->json([
                'success' => true,
                'merchants' => $merchants,
                'total' => $total
            ]);
        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function operation(string $uuid, Request $request)
    {
        try{

            //No $request contém as informações do guest (Solicitante da requisição)
            //Por isso está sendo passado para service
            
            $this->merchantsServices->operation($uuid, $request->all());

            return response()->json([
                'success' => true
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ], $e->getCode());
        }
    }
}
