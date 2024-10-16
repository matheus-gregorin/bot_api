<?php

namespace App\Repository;

use App\Models\LoginLogout;
use Exception;
use Illuminate\Support\Facades\Log;

class LoginLogoutRepository
{
    private LoginLogout $loginLogoutModel;

    public function __construct(LoginLogout $loginLogoutModel) {
        $this->loginLogoutModel = $loginLogoutModel;
    }

    public function login(array $data)
    {
        try{

            $this->loginLogoutModel->create($data);

        } catch(Exception $e){
            Log::info("Error in login", ['message' => $e->getMessage()]);
        }
    }

    public function logout(array $data)
    {
        try{

            $this->loginLogoutModel->create($data);

        } catch (Exception $e){
            Log::info("Error in logout", ['message' => $e->getMessage()]);
        }
    }
}
