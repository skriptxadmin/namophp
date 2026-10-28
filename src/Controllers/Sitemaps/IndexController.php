<?php
namespace App\Controllers\Sitemaps;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IndexController extends Controller
{
    public function index(
        Request $request,
        Response $response,
        array $args
    ): Response {
        $baseUrl = rtrim($_ENV['APP_URL'], '/');

        $urls = [
            $baseUrl . '/',
        ];

             // Remove duplicate URLs and reindex.
        $urls = array_values(array_unique($urls));

        return $this->xml(
            $request,
            'sitemaps/index',
            compact('urls')
        );
    }
}
