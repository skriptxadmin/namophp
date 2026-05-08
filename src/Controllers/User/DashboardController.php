<?php
namespace App\Controllers\User;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class DashboardController extends Controller
{

    public function index(Request $request, Response $response, array $args): Response
    {

        return $this->view($request, 'dashboard/index');

    }

    public function get(Request $request, Response $response, array $args): Response
    {

        $user_id = $request->getAttribute('user_id');

        $user = new \App\Models\User($user_id);

        $role = $user->role();

        if (empty($role) || empty($role['roleSlug'])) {

            return $this->json([]);
        }

        $roleSlug = $role['roleSlug'];

        $statuses = $this->db->select('status', ['name', 'slug', 'id'], ['deleted_at' => null]);

        if ($roleSlug == 'client') {

            $where['user_id'] = $user_id;
        }

        foreach ($statuses as &$status) {

            $where['status_id'] = [$status['id']];

            $status['count'] = $this->db->count('tickets',  $where);
        }

        return $this->json(compact('statuses'));
    }

}
