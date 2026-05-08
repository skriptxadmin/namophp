<?php 

return [
    [
            'role_id' =>1,
             'username' => "administrator",
            'fullname' => "Administrator",
            'email' => "administrator@example.com",
            'mobile' => "9876543210",
            'password' =>password_hash('Password@123', PASSWORD_DEFAULT),
    ],
     [
            'role_id' =>2,
             'username' => "agent",
            'fullname' => "Agent",
            'email' => "agent@example.com",
            'mobile' => "9876543211",
            'password' =>password_hash('Password@123', PASSWORD_DEFAULT),
    ],
     [
            'role_id' =>3,
             'username' => "client",
            'fullname' => "Client",
            'email' => "client@example.com",
            'mobile' => "9876543212",
            'password' =>password_hash('Password@123', PASSWORD_DEFAULT),
    ],
     [
            'role_id' =>3,
             'username' => "client-2",
            'fullname' => "Client 2",
            'email' => "client2@example.com",
            'mobile' => "9876543213",
            'password' =>password_hash('Password@123', PASSWORD_DEFAULT),
    ]
];