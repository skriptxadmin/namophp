<?php
namespace App\Middlewares\Web;

use function _\get;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as RequestHandler;
use Slim\Routing\RouteContext;

class UserMiddleware implements MiddlewareInterface
{
    private $args;

    public function __construct($args = null)
    {
        $this->args = $args;
    }
    public function process(Request $request, RequestHandler $handler): Response
    {
        // Optional: Handle the incoming request
        // ...

        $username = get($_SESSION, 'user_username', null);

        if (empty($username)) {
            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $url         = $routeParser->urlFor('web.login');
            $response    = $handler->handle($request);
            $uri         = $request->getUri();
            return $response
                ->withHeader('Location', $url . '?redirect=' . $uri)
                ->withStatus(302);
        }

        $dbconn = new \App\Helpers\DB;

        $join = [
            '[>]roles(r)' => ['u.role_id' => 'id'],
        ];

        $select = [
            'r.name(role)',
            'u.id',
            'u.username',
            'u.blocked_at',
        ];
        $user = $dbconn->db->get('users(u)', $join, $select, ['username' => $username]);

        if (! empty($this->args)) {

            $role = $user['role'];

            if (empty($role) || ! in_array($role, $this->args)) {

                $routeParser = RouteContext::fromRequest($request)->getRouteParser();
                $url         = $routeParser->urlFor('web.404');
                $response    = $handler->handle($request);
                $uri         = $request->getUri();
                return $response
                    ->withHeader('Location', $url)
                    ->withStatus(302);
            }

        }

        if ($user['blocked_at']) {

            $routeParser = RouteContext::fromRequest($request)->getRouteParser();
            $url         = $routeParser->urlFor('web.404');
            $response    = $handler->handle($request);
            $uri         = $request->getUri();
            return $response
                ->withHeader('Location', $url)
                ->withStatus(302);

        }

        $request = $request->withAttribute('user_id', $user['id']);

        // Invoke the next middleware and get response
        $response = $handler->handle($request);

        // Optional: Handle the outgoing response
        // ...

        return $response;
    }
}
