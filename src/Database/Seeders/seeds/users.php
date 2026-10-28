<?php

return [
    [
        'role_id'  => 1,
        'username' => 'administrator',
        'fullname' => "Administrator",
        'mobile'   => "9876543210",
        'password' => password_hash('Password@123', PASSWORD_DEFAULT),
    ],
    [
        'role_id'  => 2,
        'username' => 'bdm',
        'fullname' => "Business Development Manager",
        'mobile'   => "9876543211",
        'password' => password_hash('Password@123', PASSWORD_DEFAULT),
    ],
    [
        'role_id'  => 3,
        'username' => 'bde',
        'fullname' => "Business Development Executive",
        'mobile'   => "9876543213",
        'password' => password_hash('Password@123', PASSWORD_DEFAULT),
    ],
    [
        'role_id'  => 4,
        'username' => "subscriber",
        'fullname' => "Subscriber",
        'mobile'   => "9876543214",
        'password' => password_hash('Password@123', PASSWORD_DEFAULT),
    ],
];
