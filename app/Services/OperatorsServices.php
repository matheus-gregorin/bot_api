<?php

namespace App\Services;

use App\Entitys\OperatorEntity;
use App\Enums\Status;
use App\Jobs\SendEmail;
use App\Repository\BlackListTokensRepository;
use App\Repository\LoginLogoutRepository;
use App\Repository\OperatorsRepository;
use Carbon\Carbon;
use DateTimeZone;
use Exception;
use Firebase\JWT\JWT;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Support\Facades\Log;
use Ramsey\Uuid\Uuid;

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

        $operatorVerify = $this->operatorsRepository->getByEmail($data['email']);
        if($operatorVerify){
            throw new Exception('Operator exists', 404);
        }

        $operator = new OperatorEntity(
            Uuid::uuid4()->toString(),
            $data['name'],
            $data['email'],
            bcrypt($data['password']),
            $data['permissions'],
            Status::$OPERATOR_STATUS_OFF,
            Carbon::now(),
            Carbon::now()
        );

        $user = $this->operatorsRepository->create($operator->toArray());
        unset($user['password']);

        try{

            // Dispatch email
            SendEmail::dispatch($user->uuid)->delay(5);

        } catch (Exception $e){
            Log::critical("Create operator error - Dispatch email", ['message' => $e->getMessage()]);
        }

        return $user;
    }

    public function login(string $email, string $password)
    {
        $operator = $this->operatorsRepository->getByEmail($email);
        if($operator && password_verify($password, $operator->getPassword())){

            $token = JWT::encode(
                ['email' => $operator->getEmail(), 'permissions' => $operator->getPermissions(), 'exp' => now()->addHours(12)->getTimestamp()],
                env('SECRET_JWT') ?? "X",
                'HS256'
            );

            $this->loginLogoutRepository->login(['operator_uuid' => $operator->getUuid(), 'log' => "Entrada : " . Carbon::now()->toString()]);
            $this->operatorsRepository->changeToOnline($operator->getUuid());
            $this->blackListTokensRepository->create(['token_jwt' => "Bearer " . $token, 'active' => true]);

            Log::info('Login Success', []);

            return ['token' => $token, 'uuid' => $operator->getUuid()];

        }

        throw new Exception("Credentials invalid", 401);
    }

    public function logout(string $token, array $data)
    {
        $operator = $this->operatorsRepository->getByEmail($data['email_guest']);
        if($operator){
            if($operator->getStatus() == Status::$OPERATOR_STATUS_ON){
                $this->loginLogoutRepository->logout(['operator_uuid' => $operator->getUuid(), 'log' => "Saída : " . Carbon::now()->toString()]);
                $this->operatorsRepository->changeToOffline($operator->getUuid());
                $this->blackListTokensRepository->setDisable($token);
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

            // Permissions
            if(!empty($data['permissions'])){
                $operator->setPermissions($data['permissions']);
            }

            // Email
            if(!empty($data['email'])){
                $operator->setEmail($data['email']);
            }

            $updated = $this->operatorsRepository->update($operator->getUuid(), $operator->toArray(false));
            if($updated){
                return $operator->toArray(true);
            }

            throw new Exception("Operator can not updated", 500);

        }

        throw new Exception("Operator not found", 401);
    }

    public function deleted(string $uuid, array $data)
    {

        checkingWhetherTheRequestWasMadeByAManager($data);

        $operator = $this->operatorsRepository->getByUuid($uuid);
        if($operator){
            return $this->operatorsRepository->deleted($operator->getUuid());
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
            $operator->setPassword("");
            return $operator->toArray(true);
        }

        throw new Exception("Operator not found", 404);
    }

    public function validToken(string $token)
    {
        $valid = $this->blackListTokensRepository->getByToken($token);
        if(empty($valid) || !$valid->active) {
            throw new Exception("User not is authenticate", 401);
        }

        return true;
    }

}