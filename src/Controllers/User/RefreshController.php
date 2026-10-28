<?php
namespace App\Controllers\User;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RefreshController extends Controller
{

    public function index(Request $request, Response $response, array $args): Response
    {

        $validator = new \App\Helpers\Validator();
        $data      = $request->getParsedBody();
        $rules     = [
            'refreshToken' => 'required|string',
        ];
        $messages = [

        ];
        $validationResult = $validator->make($data, $rules, $messages);
        if ($validationResult !== true) {
            return $this->json(['errors' => $validationResult], 422);
        }
        $validData = $validator->validData;

        $jwt = new \App\Helpers\JWT;

        try {

            $decoded = $jwt->decode($validData->refreshToken);

            if (is_string($decoded)) {

                return $this->json(['error' => $decoded], 422);
            }

            if (empty($decoded->data->username)) {

                return $this->json(['error' => 'No user found'], 422);

            }

            $row = $this->db->get('users', 'id', ['username' => $decoded->data->username]);

            if (! intval($row)) {

                return $this->json(['error' => 'No user found'], 422);
            }

            $data = [
                'username' => $decoded->data->username,
                'role'     => $decoded->data->role,
            ];

            $accessToken = $jwt->encode($data);

            return $this->json(compact('accessToken'));

        } catch (\Exception $e) {

            return $this->json(['error' => 'Invalid refresh token'], 422);
        }

    }

}
