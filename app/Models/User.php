<?php

namespace App\Models;

use Bcrypt\Bcrypt;

class User extends Model
{

    private $algo = '2a';

    protected $fillable = ['name', 'username', 'email', 'password', 'mobile', 'role_id'];

    protected $hidden = ['id', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    public function setPasswordAttribute($value)
    {

        $bcrypt = new Bcrypt();

        $crypted = $bcrypt->encrypt($value, $this->algo);

        return $this->attributes['password'] = $crypted;
    }

    public function verify_password($plaintext)
    {

        $bcrypt = new Bcrypt();

        return $bcrypt->verify($plaintext, $this->password);
    }

    public function setRoleIdAttribute($value)
    {

        $role = \App\Models\Role::where('name', $value)->first();

        if (empty($role)) {

            $role = \App\Models\Role::where('name', $_ENV['DEFAULT_ROLE'])->first();

        }

        return $this->attributes['role_id'] = $role? $role->id:0;
    }

    public function role(){

        return $this->hasOne('App\Models\Role', 'id', 'role_id');

    }


}
