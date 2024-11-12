<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Jenssegers\Mongodb\Eloquent\Model;// IMPORTANTISSIMO CONFIGURAR ESSA PARTE - DE Illuminate\Database\Eloquent\Model para -> Jenssegers\Mongodb\Eloquent\Model;

class BlackListTokens extends Model
{

    use HasFactory;

    protected $connection = 'mongodb';

    /**
     * Nome da tabela no banco de dados.
     *
     * @var string
     */
    protected $table = 'blacklist_tokens';

    protected $fillable = [
        'token_jwt',
        'active',
        'updated_at',
        'created_at'
    ];

}
