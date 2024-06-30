<?php


use App\Models\Role;

$roles = [ 'Super Administrator', 'Subscriber'];

foreach($roles as $role){

    Role::create(['name' => $role]);
}