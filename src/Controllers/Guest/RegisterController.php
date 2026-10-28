<?php
namespace App\Controllers\Guest;

use App\Controllers\Controller;
use Carbon\Carbon;
use function _\get;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class RegisterController extends Controller
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

        return $this->html($request, 'guest/register');
    }

    public function verify(Request $request, Response $response, array $args): Response
    {
        $validator = new \App\Helpers\Validator();
        $data      = $request->getParsedBody();
        $rules     = [
            'mobile'   => 'required|regex:/^[6-9][0-9]{9}$/|unique:users,mobile',
            'fullname' => 'required|min:3|max:40|regex:/^[A-Za-z\s]+$/',
        ];
        $messages = [

        ];
        $validationResult = $validator->make($data, $rules, $messages);
        if ($validationResult !== true) {
            return $this->json(['errors' => $validationResult], 422);
        }
        $validData = $validator->validData;

        $random = new \App\Helpers\Random;
        $otp    = $random->otp();

        $role_id = $this->db->get('roles', 'id', ['name' => 'Subscriber']);

        $userHelper = new \App\Helpers\User;

        $args = [
            'username'       => $userHelper->create_username($validData->fullname),
            'password'       => password_hash($random->string(), PASSWORD_DEFAULT),
            'fullname'       => $validData->fullname,
            'mobile'         => $validData->mobile,
            'role_id'        => $role_id,
            'otp'            => $otp,
            'otp_created_at' => Carbon::now()->toDateTimeString(),
        ];

        try {
            $where = [
                'mobile' => $validData->mobile,

            ];
            $existing_id = $this->db->get('users_temp', 'id', $where);

            if ($existing_id) {

                $this->db->update('users_temp', $args, ['id' => $existing_id]);
            } else {

                $this->db->insert('users_temp', $args);

            }

        } catch (\Exception $e) {

            return $this->json(['error' => $e->getMessage()], 422);
        }

        $ENV = get($_ENV, 'APP_ENV', 'production');

        $res = [
            'message'  => 'User registration successful',
            'username' => $args['username'],
        ];

        if ($ENV === 'production') {

            $fast2sms = new \App\Helpers\Fast2Sms;
            $fast2sms->send($validData->mobile, $otp);
            return $this->json($res);

        }
        $res['otp'] = $otp;

        return $this->json($res);

    }
}
