<?php
namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class TestController extends Controller
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

        // Sample for html output

        $keywords = $this->db->select('keywords', ['slug', 'name', 'id']);

        $output = [];
        foreach($keywords as $keyword){
            $data = [
            ];
            if(strpos($keyword['slug'], '-tindivanam')){
                $data['slug'] = trim($keyword['slug'], '-tindivanam');
            }
            if(strpos($keyword['name'], ' Tindivanam')){
                $data['name'] = trim($keyword['name'], ' Tindivanam');
            }

            if(!empty($data)){
                $this->db->update('keywords', $data, ['id' => $keyword['id']]);
            $output[] = $data;

            }
        }

        return $this->json([$output]);
    }
}
