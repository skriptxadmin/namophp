<?php
namespace App\Controllers\Guest;

use App\Controllers\Controller;
use Carbon\Carbon;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class SetPasswordApiController extends Controller
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
       $username = $args['username']??null;

       $queryParams =  $request->getQueryParams();

       $otp = $queryParams['otp']??'';

        return $this->html($request, 'guest/set-password', compact('username', 'otp'));
    }

    public function verify(Request $request, Response $response, array $args): Response
    {
        $validator = new \App\Helpers\Validator();
        $data      = $request->getParsedBody();
        $rules     = [
            'username'  => 'required',
            'password'  => 'required|min:8|max:16',
            'cpassword' => 'required|same:password',
            'otp'       => 'required|min:6|max:6|regex:/^\d{6}$/',
        ];
        $messages = [
        ];
        $validationResult = $validator->make($data, $rules, $messages);
        if ($validationResult !== true) {
            return $this->json(['errors' => $validationResult], 409);
        }
        $validData = $validator->validData;

        $where = [
            'username'          => $validData->username,
            'otp'               => $validData->otp,
            'otp_created_at[!]' => null,
        ];

        $user    = (object) $this->db->get('users', '*', $where);
        $is_temp = 0;
        if (empty($user->id)) {

            $user = (object) $this->db->get('users_temp', '*', $where);

            if (empty($user->id)) {

                return $this->json(['error' => 'Invalid user'], 422);
            }

            $is_temp = 1;

        }

        $user_id = $user->id;

        $createdAt = Carbon::parse($user->otp_created_at);

        // Get the difference in minutes
        $minutesPassed = $createdAt->diffInMinutes(Carbon::now());

        if ($minutesPassed > 10) {

            return $this->json(['message' => 'OTP expired. Try again'], 422);
        }

        $args = [
            'password'       => password_hash($validData->password, PASSWORD_DEFAULT),
            'otp'            => null,
            'otp_created_at' => null,
            'verified_at'    => Carbon::now()->toDateTimeString(),
        ];

        if ($is_temp) {
            $args['username'] = $user->username;
            $usernameCount = $this->db->count('users', ['username' => $user->username]);
            if ($usernameCount) {
                $userHelper = new \App\Helpers\User;
                $args['username'] = $userHelper->create_username($user->fullname);
            }
            $args['role_id'] = $user->role_id;
            $args['fullname'] = $user->fullname;
            $args['mobile']   = $user->mobile;
            $this->db->insert('users', $args);
            $this->db->delete('users_temp', ['id' => $user_id]);

        } else {
            $this->db->update('users', $args, ['id' => $user_id]);

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
            'username' => $user->username,
            'role'     => $role,
        ];

        return $this->json(compact('accessToken', 'refreshToken', 'user'));

    }
}
