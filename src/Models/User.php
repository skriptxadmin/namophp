<?php

namespace App\Models;

class User extends Model{

    private $user_id;

    public function __construct($user_id){
        parent::__construct();
        $this->user_id = $user_id;
    }

    public function role(){
         $join = [
            '[>]roles(r)' => ['u.role_id' => 'id']
        ];

        $select = [
            'r.name(roleName)',
            'r.slug(roleSlug)'
        ];

        $where = ['u.id' => $this->user_id];

        $role = $this->db->get('users(u)', $join, $select, $where);

        return $role;
    }
}