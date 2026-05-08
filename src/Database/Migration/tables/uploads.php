<?php

return [
    'id'         => [
        "INT",
        "NOT NULL",
        "AUTO_INCREMENT",
        "PRIMARY KEY",
    ],
    'user_id'    => [
        "INT(10)",
        "NOT NULL",
    ],
     'filename'        => [
        "TEXT",
        "NULL",
    ],
    'key'        => [
        "TEXT",
        "NULL",
    ],
    'mime'       => [
        "TEXT",
        "NULL",
    ],
    'size'       => [
        "INT(10)",
        "NULL",
    ],
    'created_at' => [
        "TIMESTAMP",
        "DEFAULT CURRENT_TIMESTAMP",
    ],
    'updated_at' => [
        "TIMESTAMP",
        "DEFAULT CURRENT_TIMESTAMP",
        "ON UPDATE CURRENT_TIMESTAMP",
    ],
];
