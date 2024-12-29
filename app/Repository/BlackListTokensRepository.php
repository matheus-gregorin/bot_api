<?php

namespace App\Repository;

use App\Models\BlackListTokens;
use Exception;

class BlackListTokensRepository
{

    private BlackListTokens $blacklistTokens;

    public function __construct(BlackListTokens $blackListTokens) {
        $this->blacklistTokens = $blackListTokens;
    }

    public function create(array $data)
    {
        try {

            $this->blacklistTokens->create($data);

        } catch (Exception $e) {
            throw new Exception("Error in insert token - " . $e->getMessage(), 500);
        }
    }

    public function getByToken(string $token)
    {
        try {

            return $this->blacklistTokens->where('token_jwt', $token)->first();

        } catch (Exception $e) {
            throw new Exception("Error in get token - " . $e->getMessage(), 500);
        }
    }

    public function setDisable(string $token)
    {
        try {

            $tokenData = $this->blacklistTokens->where('token_jwt', $token)->first();
            if(!empty($tokenData)){
                $tokenData->active = false;
                $tokenData->save();
            }

        } catch (Exception $e) {
            throw new Exception("Error in get token disable - " . $e->getMessage(), 500);
        }
    }

    public function delete(string $operatorUuid)
    {
        try {

            $tokenData = $this->blacklistTokens->where('operator_uuid', $operatorUuid)->first();
            if(!empty($tokenData)){
                $tokenData->delete();
            }

        } catch (Exception $e) {
            throw new Exception("Error in get token disable - " . $e->getMessage(), 500);
        }
    }

}
