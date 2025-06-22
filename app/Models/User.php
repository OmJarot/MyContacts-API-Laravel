<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    protected $table = "users";
    protected $primaryKey = "id";
    protected $keyType = "int";
    public $timestamps = true;
    public $incrementing = true;

    protected $fillable = [
        "username",
        "password",
        "name"
    ];

    function contacts(): HasMany {
        return $this->hasMany(Contact::class, "user_id", "id");
    }

    public function getRememberTokenName() {
        return 'token';
    }
}
