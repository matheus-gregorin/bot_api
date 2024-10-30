<?php

use App\Http\Controllers\ClientsControllers;
use App\Http\Controllers\ItemsControllers;
use App\Http\Controllers\ListOfPurchaseControllers;
use App\Http\Controllers\MerchantsControllers;
use App\Http\Controllers\OperatorsControllers;
use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Twilio\Rest\Client;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['prefix' => 'auth', 'middleware' => 'jwt'], function(){
    Route::post('/login', [OperatorsControllers::class, 'login'])->withoutMiddleware('jwt');
    Route::post('/logout', [OperatorsControllers::class, 'logout']);
});

Route::group(['prefix' => 'merchants', 'middleware' => 'jwt'], function(){
    Route::post('/create', [MerchantsControllers::class, 'create']);
    Route::put('/updated/{uuid}', [MerchantsControllers::class, 'updated']);
    Route::delete('/deleted/{uuid}', [MerchantsControllers::class, 'deleted']);
    Route::get('/all', [MerchantsControllers::class, 'all']);
    Route::get('/get/{uuid}', [MerchantsControllers::class, 'get']);
    Route::post('/operation/{uuid}', [MerchantsControllers::class, 'operation']);
});

Route::group(['prefix' => 'operators', 'middleware' => 'jwt'], function(){
    Route::post('/create', [OperatorsControllers::class, 'create']);
    Route::put('/updated/{uuid}', [OperatorsControllers::class, 'updated']);
    Route::delete('/deleted/{uuid}', [OperatorsControllers::class, 'deleted']);
    Route::get('/all', [OperatorsControllers::class, 'all']);
    Route::get('/get/{uuid}', [OperatorsControllers::class, 'get']);
});

Route::group(['prefix' => 'clients', 'middleware' => 'jwt'], function(){
    Route::post('/create', [ClientsControllers::class, 'create']);
    Route::put('/updated/{uuid}', [ClientsControllers::class, 'updated']);
    Route::delete('/deleted/{uuid}', [ClientsControllers::class, 'deleted']);
    Route::get('/all', [ClientsControllers::class, 'all']);
    Route::get('/get/{uuid}', [ClientsControllers::class, 'get']);
});

Route::group(['prefix' => 'items', 'middleware' => 'jwt'], function(){
    Route::post('/create', [ItemsControllers::class, 'create']);
    Route::put('/updated/{uuid}', [ItemsControllers::class, 'updated']);
    Route::delete('/deleted/{uuid}', [ItemsControllers::class, 'deleted']);
    Route::get('/all/{merchantUuid}', [ItemsControllers::class, 'allByMerchant']);
    Route::get('/get/{uuid}', [ItemsControllers::class, 'get']);
});

Route::group(['prefix' => 'list', 'middleware' => 'jwt'], function(){
    Route::post('/create', [ListOfPurchaseControllers::class, 'create']);
    Route::put('/update-items/{uuid}', [ListOfPurchaseControllers::class, 'updateItems']);
    Route::put('/update/{uuid}', [ListOfPurchaseControllers::class, 'update']);
    Route::delete('/delete/{uuid}', [ListOfPurchaseControllers::class, 'delete']);
    Route::get('/all', [ListOfPurchaseControllers::class, 'getAll']);
    Route::get('/get/{uuid}', [ListOfPurchaseControllers::class, 'get']);
});

// ARQUIVO NA RAIZ CHAMADO INFO_GEMINI com todos os recursos
Route::group(['prefix' => 'webhook'], function(){
    Route::post('/send', function(Request $request){

        $data = $request->all();

        //$name = $data['ProfileName'];
        $number = $data['From'];
        $message = $data['Body'];

        $chat = Gemini::startChat();
        $response = $chat->sendMessage($message);
        
        $sid = env('ID_TWILLIO');
        $token = env('TOKEN_TWILLIO');
        $client = new Client($sid, $token); //Twillio

        try{

        //Use the Client to make requests to the Twilio REST API
        //Use o cliente para fazer solicitações à API REST da Twilio
        $client->messages->create(
            //The number you'd like to send the message to
            //O número para o qual você gostaria de enviar a mensagem
            $number,
            [
                //A Twilio phone number you purchased at https://console.twilio.com
                //Um número de telefone da Twilio que você comprou em https://console.twilio.com
                'from' => 'whatsapp:+14155238886',

                //The body of the text message you'd like to send
                //O corpo da mensagem de texto que você deseja gostaria de enviar
                'body' => $response
            ] 
        );

        //return $response;

        } catch (Exception $e){
            return $e->getMessage();
        }

    });

});