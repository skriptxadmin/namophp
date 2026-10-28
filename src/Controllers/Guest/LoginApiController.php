<?php
namespace App\Controllers\Guest;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginApiController extends Controller
{
    /**
     * Handle guest login request.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */

    public function verify(Request $request, Response $response, array $args): Response
    {

        $validator = new \App\Helpers\Validator();
        $data      = $request->getParsedBody();
        $rules     = [
            'mobile'   => 'required',
            'password' => 'required|min:6',
        ];
        $messages = [

        ];
        $validationResult = $validator->make($data, $rules, $messages);
        if ($validationResult !== true) {
            return $this->json(['errors' => $validationResult], 422);
        }
        $validData = $validator->validData;

        $where = [
            'mobile' => $validData->mobile,

        ];

        $user = (object) $this->db->get('users', '*', $where);

        if (empty($user->id)) {

            return $this->json(['error' => 'Invalid credentials'], 422);
        }
        if (! password_verify($validData->password, $user->password)) {
            return $this->json([
                'error' => 'Invalid credentials',
            ], 422);
        }

        if (! empty($user->blocked_at)) {

            return $this->json(['error' => 'Your account is restricted from administrator. Please contact administrator'], 422);

        }
        $role = $this->db->get('roles', 'slug', ['id' => $user->role_id]);

        $data = [
            'username' => $user->username,
            'role'     => $role,
        ];

        $jwt = new \App\Helpers\JWT;

        $accessToken = $jwt->encode($data);

        $refreshToken = $jwt->refresh_token($data);

        $user = [
            'fullname' => $user->fullname,
            'role'     => $role,
        ];

        return $this->json(compact('accessToken', 'refreshToken', 'user'));
    }
}
