<?php


use App\Models\User;

$users = [
    [
        "name" => "Super Administrator",
        "role_id" => "Super Administrator",
        "email" => "superadmin@example.com",
        "mobile" => "1111111111",
        "password" => "Password@123",
        "username"=> "superadmin",
    ]
    ];

foreach($users as $user){


    User::create($user);
}