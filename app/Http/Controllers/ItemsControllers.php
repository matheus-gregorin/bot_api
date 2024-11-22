<?php

namespace App\Http\Controllers;

use App\Services\ItemsServices;
use Exception;
use Illuminate\Http\Request;

class ItemsControllers extends Controller
{
    private ItemsServices $itemsServices;

    public function __construct(ItemsServices $itemsServices) {
        $this->itemsServices = $itemsServices;
    }

    public function create(Request $request)
    {
        try{

            $item = $this->itemsServices->create($request->all());

            return response()->json([
                'success' => true,
                'item' => $item
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

            $item = $this->itemsServices->updated($uuid, $request->all());

            return response()->json([
                'success' => true,
                'item' => $item
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

            $this->itemsServices->deleted($uuid, $request->all());

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

    public function all(Request $request)
    {
        try{

            $items = $this->itemsServices->all( $request->all());
            $total = $items['total'] ?? 0;
            unset($items['total']);

            return response()->json([
                'success' => true,
                'items' => $items,
                'total' => $total
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }

    public function AllByMerchant(string $merchantUuid, Request $request)
    {
        try{

            $items = $this->itemsServices->allByMerchant($merchantUuid, $request->all());
            $total = $items['total'] ?? 0;
            unset($items['total']);

            return response()->json([
                'success' => true,
                'items' => $items,
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

            $item = $this->itemsServices->get($uuid, $request->all());

            return response()->json([
                'success' => true,
                'item' => $item
            ]);

        } catch (Exception $e){
            return response()->json([
                'success' => false, 
                'message' => $e->getMessage()
            ]);
        }
    }
}
