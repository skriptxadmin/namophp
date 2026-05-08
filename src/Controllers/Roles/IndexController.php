<?php
namespace App\Controllers\Roles;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IndexController extends Controller
{

    public function index(Request $request, Response $response, array $args): Response
    {

        return $this->view($request, 'roles/index');
    }

    public function list(Request $request, Response $response, array $args): Response
    {
       
        $roles = $this->db->select('roles', ['slug', 'name']);

        return $this->json(compact('roles'));
    }

}
