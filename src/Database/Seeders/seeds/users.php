<?php 

return [
    [
            'role_id' =>1,
             'slug' => "administrator",
            'fullname' => "Administrator",
            'email' => "administrator@example.com",
            'mobile' => "9876543210",
            'password' =>password_hash('Password@123', PASSWORD_DEFAULT),
    ]
];