<?php

namespace App\Models;


class Role extends Model{


    protected $fillable = ['name'];

    protected $hidden = ['id', 'created_by', 'updated_by', 'deleted_by', 'created_at', 'updated_at', 'deleted_at'];

    
}