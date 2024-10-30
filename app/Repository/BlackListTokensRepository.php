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

    public function delete(string $token)
    {
        // todo
    }

}
