<?php
namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ExampleController extends Controller
{
    /**
     * Handle guest login request.
     *
     * @param Request $request
     * @param Response $response
     * @param array $args
     * @return Response
     */

    public function html(Request $request, Response $response, array $args): Response
    {

        // Sample for html output

        return $this->view($request, 'home/index');
    }

    public function json(Request $request, Response $response, array $args): Response
    {

        // Sample for json output

        return $this->json(['success' => true], 422);
    }

    public function profiles(Request $request, Response $response, array $args): Response
    {
        //  Sample for file caching

        $start = microtime(true);

        $cachedfilename = 'home-profiles';

        $data = $this->get_cached_json($cachedfilename);

        if (! empty($data)) {

            return $this->json($data);
        }

        // sql logic to get data from database

        $data = compact('records');

        $end = microtime(true);

        $executionTime = $end - $start; // in seconds
        $mins          = floor($executionTime / 60);
        $secs          = $executionTime % 60;

        $data['time_taken'] = "{$mins}:" . round($secs, 2);

        $this->set_cached_json($cachedfilename, $data);

        return $this->json($data);
    }
}
