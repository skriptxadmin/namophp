<?php
namespace App\Controllers\Users;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Carbon\Carbon;

class IndexController extends Controller
{
    public function list(Request $request, Response $response, array $args): Response
    {
        $queryParams = $request->getQueryParams();

        $q = trim($queryParams['q'] ?? '');
        $r = trim($queryParams['r'] ?? '');

        $page         = max(1, (int) ($queryParams['page'] ?? 1));
        $itemsPerPage = max(1, (int) ($queryParams['itemsPerPage'] ?? 10));

        $offset = ($page - 1) * $itemsPerPage;

        $join = [
            '[>]roles(r)' => ['role_id' => 'id'],
        ];

        $select = [
            'u.username',
            'u.fullname',
            'u.mobile',
            'u.blocked_at',
            'r.name(role)',
            'r.slug(role_slug)',
        ];

        $where = [];

        if ($q !== '') {
            $where['OR'] = [
                'u.username[~]' => $q,
                'u.fullname[~]' => $q,
                'u.mobile[~]'   => $q,
            ];
        }

        if ($r !== '') {
            $roleId = $this->db->get('roles', 'id', [
                'slug' => $r,
            ]);

            if ($roleId) {
                $where['u.role_id'] = $roleId;
            }
        }

        $total    = $this->db->count('users');
        $filtered = $this->db->count('users(u)', $where);

        $where['LIMIT'] = [$offset, $itemsPerPage];
        $where['ORDER'] = ['u.fullname' => 'ASC'];

        $users = $this->db->select('users(u)', $join, $select, $where);

        $pages = max(1, (int) ceil($filtered / $itemsPerPage));

        $from = $filtered > 0 ? $offset + 1 : 0;
        $to   = min($offset + $itemsPerPage, $filtered);

        $pagination = [
            'page'         => $page,
            'itemsPerPage' => $itemsPerPage,
            'total'        => $total,
            'filtered'     => $filtered,
            'pages'        => $pages,
            'hasPrevPage'  => $page > 1,
            'hasNextPage'  => $page < $pages,
            'from'         => $from,
            'to'           => $to,
        ];

        return $this->json([
            'users'      => $users,
            'pagination' => $pagination,
        ]);
    }

    public function block(Request $request, Response $response, array $args): Response
    {

        $validator = new \App\Helpers\Validator();
        $data      = $request->getParsedBody();
        $rules     = [
            'username' => 'required|exists:users,username',
            'blocked'  => 'required|boolean',
        ];
        $messages = [

        ];
        $validationResult = $validator->make($data, $rules, $messages);
        if ($validationResult !== true) {
            return $this->json(['errors' => $validationResult], 422);
        }
        $validData = $validator->validData;

        $where = [
            'username'   => $validData->username,
            'deleted_at' => null,
            'id[!]'    => [1]
        ];

        $block = json_decode($validData->blocked);

        if ($block) {
            $where['blocked_at'] = null;
        } else {
            $where['blocked_at[!]'] = null;
        }

        $user_id = $this->db->get('users', 'id', $where);

        if (empty($user_id)) {

            return $this->json(['error' => 'Invalid user'], 422);
        }

        $now = Carbon::now()->toDateTimeString();

        $data = [
            'blocked_at' => $now
        ];

        $this->db->update('users', $data, $where);

        return $this->json(['success'=>true]);

    }
}
