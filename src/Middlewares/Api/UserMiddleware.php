<?php
namespace App\Middlewares\Api;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;

class UserMiddleware implements MiddlewareInterface
{
    private $args;

    public function __construct($args = null)
    {
        $this->args = $args;
    }

    public function process(Request $request, RequestHandler $handler): Response
    {

    
        // Invoke the next middleware and get response
        $jwt = new \App\Helpers\JWT;

        $uid = $jwt->getUid($request);

        if (empty($uid) || is_string($uid) || ! is_numeric($uid)) {

            $payload = json_encode(['error' => 'You are not authorized to perform this action', 'uid' => $uid]);

            $response = new \Slim\Psr7\Response();

            $response->getBody()->write($payload);

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(422);
        }

           $dbconn = new \App\Helpers\DB;

        if (! empty($this->args)) {

            $where = ['u.id' => $uid];

            $join = [
                '[>]roles(r)' => ['role_id' => 'id'],
            ];

            $select = 'r.name';

            $role = $dbconn->db->get('users(u)', $join, $select, $where);

            if (empty($role) || ! in_array($role, $this->args)) {

                $payload = json_encode(['error' => 'You are not authorized to perform this action']);

                $response = new \Slim\Psr7\Response();

                $response->getBody()->write($payload);

                return $response
                    ->withHeader('Content-Type', 'application/json')
                    ->withStatus(422);
            }

        }

        $count = $dbconn->db->count('users', ['blocked_at[!]' => null, 'id' => $uid]);

        if ($count) {

            $payload = json_encode(['error' => 'You are not authorized to perform this action']);

            $response = new \Slim\Psr7\Response();

            $response->getBody()->write($payload);

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withStatus(422);

        }

        
        $request = $request->withAttribute('user_id', $uid);

        $response = $handler->handle($request);

        return $response;
    }
}
