<?php
namespace App\Helpers;

use App\Helpers\DB;
use function _\kebabCase;

class User extends DB
{

    public function get_role($user_id)
    {

        $role_id = $this->db->get('users', 'role_id', ['id' => $user_id]);

        if (empty($role_id)) {

            return false;
        }

        $role = $this->db->get('roles', 'slug', ['id' => $role_id]);

        return $role;

    }

    public function create_username($title)
    {
        $slug  = kebabCase($title);
        $count = $this->db->count('users', ['username' => $slug]);
        if (! $count) {
            return $slug;
        }

        $random = new \App\Helpers\Random;
        $slug   = null;
        $loop   = 1;
        $char   = 4;
        do {
            $slug = $random->slug($title, $char);

            $count = $this->db->count('users', ['username' => $slug]);

            if ($count) {

                $loop++;
            }
            if ($loop > 99) {

                $loop = 1;
                $char = $char + 1;
            }

        } while ($count);

        return $slug;
    }

}
