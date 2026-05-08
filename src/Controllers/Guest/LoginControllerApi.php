<?php
namespace App\Controllers\Guest;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class LoginControllerApi extends Controller
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
            'username' => 'required',
            'password' => 'required|min:6',
        ];
        $messages = [
            'username.required' => 'Username is required',
        ];
        $validationResult = $validator->make($data, $rules, $messages);
        if ($validationResult !== true) {
            return $this->json(['errors' => $validationResult], 422);
        }
        $validData = $validator->validData;

        $where = [
            'OR' => [
                'username' => $validData->username,
                'email'  => $validData->username,
                'mobile' => $validData->username,
            ],
        ];

        $user = (object) $this->db->get('users', '*', $where);
        
        if (empty($user->id)) {

            return $this->json(['error' => 'Invalid email or mobile specified'], 422);
        }
        if (! password_verify($validData->password, $user->password)) {
            return $this->json([
                'error' => 'Invalid credentials',
            ], 422);
        }

        if(!empty($user->blocked_at)){

            return $this->json(['error' => 'Your account is restricted from administrator. Please contact administrator'], 422);

        }
        $role = $this->db->get('roles', 'slug', ['id' => $user->role_id]);


        $data = [
            'username' => $user->username,
            'role' => $role
        ];

        $jwt = new \App\Helpers\JWT;

        $token = $jwt->encode($data);

        $refresh =  $jwt->set_refresh_token($user->id);


        return $this->json(compact('token', 'refresh'));
    }
}
