<?php

namespace App\Services;

use App\Jobs\SendEmail;
use App\Repository\BlackListTokensRepository;
use App\Repository\LoginLogoutRepository;
use App\Repository\OperatorsRepository;
use Carbon\Carbon;
use Exception;
use Firebase\JWT\JWT;
use GeminiAPI\Laravel\Facades\Gemini;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;
use Twilio\Rest\Client;

class OperatorsServices
{
    use DispatchesJobs;

    private OperatorsRepository $operatorsRepository;
    private LoginLogoutRepository $loginLogoutRepository;
    private BlackListTokensRepository $blackListTokensRepository;

    public function __construct(
        OperatorsRepository $operatorsRepository,
        LoginLogoutRepository $loginLogoutRepository,
        BlackListTokensRepository $blackListTokensRepository
    ) 
    {
        $this->operatorsRepository = $operatorsRepository;
        $this->loginLogoutRepository = $loginLogoutRepository;
        $this->blackListTokensRepository = $blackListTokensRepository;
    }

    public function create(array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $operatorVerify = $this->operatorsRepository->get($data['email']);
        if($operatorVerify){
            throw new Exception('Operator exists', 404);
        }

        $data = ["uuid" => Uuid::uuid4()->toString()] + $data;
        $data['password'] = bcrypt($data['password']);
        $data['status'] = "Offline";

        $user = $this->operatorsRepository->create($data);
        unset($user['password']);

        try{

            // Dispatch email
            SendEmail::dispatch($user->uuid)->delay(2);

        } catch (Exception $e){
            Log::channel('stderr')->info($e->getMessage());
        }

        return $user;
    }

    public function login(string $email, string $password)
    {
        Log::info('', ['email'=> $email]);
        $operator = $this->operatorsRepository->get($email);
        if($operator && password_verify($password, $operator->password)){

            $token = JWT::encode(
                ['name' => $operator->name, 'permissions' => $operator->permissions, 'exp' => now()->addHours(3)->getTimestamp()],
                env('SECRET_JWT') ?? "X",
                'HS256'
            );

            $this->loginLogoutRepository->login(['operator_uuid' => $operator->uuid, 'log' => "Entrada : " . Carbon::now()->toString()]);
            $this->operatorsRepository->changeToOnline($operator->uuid);

        return $token;

        }

        throw new Exception("Credentials invalid", 401);
    }

    public function logout(string $token, array $data)
    {
        $operator = $this->operatorsRepository->get($data['name_guest']);
        if($operator){
            if($operator->status == "Online"){
                $this->loginLogoutRepository->logout(['operator_uuid' => $operator->uuid, 'log' => "Saída : " . Carbon::now()->toString()]);
                $this->operatorsRepository->changeToOffline($operator->uuid);
                $this->blackListTokensRepository->create(['token_jwt' => $token]);
    
                return;
            }
            throw new Exception("Operator is already offline", 401);
        }

        throw new Exception("Operator not found", 401);
    }

    public function updated(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $operator = $this->operatorsRepository->getByUuid($uuid);
        if($operator){

            //permissions
            if(!empty($data['permissions'])){
                $operator->permissions = $data['permissions'];
            }

            if(!empty($data['email'])){
                $operator->email = $data['email'];
            }

            return $operator->save();
        }

        throw new Exception("Operator not found", 401);
    }

    public function deleted(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $operator = $this->operatorsRepository->getByUuid($uuid);
        if($operator){
            return $this->operatorsRepository->deleted($operator->uuid);
        }

        throw new Exception("Operator not found", 401);
    }

    public function all(array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        return $this->operatorsRepository->listAll($data);
    }

    public function get(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $operator = $this->operatorsRepository->getByUuid($uuid);
        if($operator){
            unset($operator['password']);
            return $operator;
        }

        throw new Exception("Operator not found", 404);
    }

}