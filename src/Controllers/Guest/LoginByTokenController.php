<?php
namespace App\Controllers\Guest;

use App\Controllers\Controller;
use function _\get;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginByTokenController extends Controller
{
    /**
     * Handle guest login request.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */

    public function index(Request $request, Response $response, array $args): Response
    {
        $query = $request->getQueryParams();

        $token = $query['token'];

        try {

            $jwt = new \App\Helpers\JWT;

            $decoded = $jwt->decode($token);

            $username = get($decoded, 'data.username', null);

            if (empty($username)) {

                return $this->redirect($request, 'web.404');

            }

            $user = $this->db->get('users', ['username', 'role_id'], ['username' => $username]);

            if (empty($user['username'])) {

                return $this->redirect($request, 'web.404');

            }

            $_SESSION['user_username'] = $user['username'];

            $role = $this->db->get('roles', 'slug', ['id' => $user['role_id']]);

            $_SESSION['user_role'] = $role;

            return $this->redirect($request, 'web.dashboard');

        } catch (\Exception $e) {

            return $this->redirect($request, 'web.404');
        }

    }
}
