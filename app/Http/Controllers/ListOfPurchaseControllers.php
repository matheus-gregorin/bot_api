<?php

namespace App\Http\Controllers;

use App\Services\ListOfPurchaseServices;
use Exception;
use Illuminate\Http\Request;

class ListOfPurchaseControllers extends Controller
{
    private ListOfPurchaseServices $listOfPurchaseServices;

    public function __construct(ListOfPurchaseServices $listOfPurchaseServices) {
        $this->listOfPurchaseServices = $listOfPurchaseServices;
    }

    public function create(Request $request)
    {
        try{

            $purchase = $this->listOfPurchaseServices->create($request->all());

            return response()->json([
                'success' => $purchase
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function updateItems(string $listUuid, Request $request)
    {
        try{

            $updated = $this->listOfPurchaseServices->updateItems($listUuid, $request->all());

            return response()->json([
                'success' => $updated
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function update(string $listUuid, Request $request)
    {
        try{

            $updated = $this->listOfPurchaseServices->update($listUuid, $request->all());

            return response()->json([
                'success' => $updated
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }
}