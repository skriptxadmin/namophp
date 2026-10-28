<?php
namespace App\Controllers\CronJobs;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class IndexController extends Controller
{

    public function index(Request $request, Response $response, array $args): Response
    {
      

        return $this->json(['success' => true]);
    }

}
