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
                'success' => true,
                'list' => $purchase
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

            $list = $this->listOfPurchaseServices->updateItems($listUuid, $request->all());

            return response()->json([
                'success' => true,
                'list' => $list
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

    public function delete(string $listUuid, Request $request)
    {
        try{

            $this->listOfPurchaseServices->delete($listUuid, $request->all());

            return response()->json([
                'success' => true
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function getAll(Request $request)
    {
        try{

            $lists = $this->listOfPurchaseServices->getAll($request->all());
            $total = $lists['total'] ?? 0;
            unset($lists['total']);;

            return response()->json([
                'success' => true,
                'lists' => $lists,
                'total' => $total
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

            $list = $this->listOfPurchaseServices->get($uuid, $request->all());

            return response()->json([
                'success' => true,
                'list' => $list
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }
}