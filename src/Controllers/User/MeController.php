<?php
namespace App\Controllers\User;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class MeController extends Controller
{

    public function index(Request $request, Response $response, array $args): Response
    {

        $user_id = $request->getAttribute('user_id');

        $user = $this->db->get('users',['fullname', 'email', 'mobile', 'username'], ['id' => $user_id]);

        return $this->json(compact('user'));

    }

}
