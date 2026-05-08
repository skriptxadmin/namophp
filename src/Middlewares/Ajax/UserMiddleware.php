<?php

namespace App\Middlewares\Ajax;

use App\Helpers\DB;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Psr7\Response as SlimResponse;

use function _\get;

class UserMiddleware implements MiddlewareInterface
{
    private ?array $allowedRoles;

    public function __construct(?array $allowedRoles = null)
    {
        $this->allowedRoles = $allowedRoles;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {
        $username = get($_SESSION, 'user_username');

        if (empty($username)) {
            return $this->jsonResponse([
                'error' => 'You are not authorized to perform this action',
                'user'  => false,
            ]);
        }

        $user = $this->getUser($username);

        if (empty($user)) {
            return $this->jsonResponse([
                'error' => 'User not found',
            ]);
        }

        if ($this->isBlocked($user)) {
            return $this->jsonResponse([
                'error'   => 'You are not authorized to perform this action',
                'blocked' => true,
            ]);
        }

        if (! $this->hasValidRole($user)) {
            return $this->jsonResponse([
                'error' => 'You are not authorized to perform this action',
                'role'  => false,
            ]);
        }

        $request = $request->withAttribute('user_id', $user['id']);
        $request = $request->withAttribute('user', $user);

        return $handler->handle($request);
    }

    private function getUser(string $username): ?array
    {
        $db = new DB();

        return $db->db->get(
            'users(u)',
            [
                '[>]roles(r)' => ['u.role_id' => 'id'],
            ],
            [
                'u.id',
                'u.username',
                'u.blocked_at',
                'r.name(role)',
            ],
            [
                'username' => $username,
            ]
        );
    }

    private function isBlocked(array $user): bool
    {
        return ! empty($user['blocked_at']);
    }

    private function hasValidRole(array $user): bool
    {
        if (empty($this->allowedRoles)) {
            return true;
        }

        return in_array($user['role'] ?? null, $this->allowedRoles, true);
    }

    private function jsonResponse(array $data, int $status = 422): Response
    {
        $response = new SlimResponse();

        $response->getBody()->write(
            json_encode($data)
        );

        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}