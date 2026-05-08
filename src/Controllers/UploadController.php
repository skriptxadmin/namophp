<?php
namespace App\Controllers;

use App\Controllers\Controller;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class UploadController extends Controller
{

    public function index(Request $request, Response $response, array $args): Response
    {

        $validator = new \App\Helpers\Validator();

        $user_id = $request->getAttribute('user_id');

        $rules = [
            'file'   => 'required|uploaded_file:0,5M',
            'folder' => 'nullable',
        ];
        $messages = [

        ];

        $validationResult = $validator->make($_POST + $_FILES, $rules, $messages);

        if ($validationResult !== true) {

            return $this->json(['errors' => $validationResult], 409);
        }

        $validData = $validator->validData;

        $uploadedFiles = $request->getUploadedFiles();

        $username = $this->db->get('users', 'username', ['id' => $user_id]);

        $uploadedFile = $uploadedFiles['file'];

        $filename = $uploadedFile->getClientFilename();

        $ext = pathinfo($filename, PATHINFO_EXTENSION);

        $nameWithoutExt = pathinfo($filename, PATHINFO_FILENAME);

        $file = $nameWithoutExt . '-' . uniqid() . '.' . strtolower($ext);

        // Generate new key
        $key = $username . "/" . $file;

        if ($validData->folder) {

            $key = $validData->folder . '/' . $username . "/" . $file;

        }

        // Final file path
        $filePath = ABSPATH . '/public/uploads/' . $key;

// Ensure directory exists
        $directory = dirname($filePath);

        if (! is_dir($directory)) {
            mkdir($directory, 0777, true);
        }

// Move uploaded file
        $uploadedFile->moveTo($filePath);

        $clientMimeType = $uploadedFile->getClientMediaType();

        $finfo        = finfo_open(FILEINFO_MIME_TYPE);
        $realMimeType = finfo_file($finfo, $filePath);
        finfo_close($finfo);

        $args = [
            'filename' => $filename,
            'user_id' => $user_id,
            'key'     => $key,
            'mime'    => $realMimeType,
            'size'    => $uploadedFile->getSize(),
        ];
        $this->db->insert('uploads', $args);

        $url = $_ENV['APP_URL'] . '/uploads/' . $key;

        return $this->json(['url' => $url, 'key' => $key, 'mime' => $realMimeType]);
    }

    public function get(Request $request, Response $response, array $args): Response
    {

        $validator = new \App\Helpers\Validator();

        $data = $request->getParsedBody();

        $rules = [
            'key' => 'required|exists:uploads,key',
        ];
        $messages = [

        ];

        $validationResult = $validator->make($data, $rules, $messages);

        if ($validationResult !== true) {

            return $this->json(['errors' => $validationResult], 409);
        }

        $validData = $validator->validData;

        $s3 = new \App\Helpers\S3;

        if (! $s3->doesObjectExist($validData->key)) {

            return $this->json(['error' => 'Invalid file to get'], 422);
        }

        $url = $s3->getPresignedUrl($validData->key);

        if (empty($url)) {

            return $this->json(['error' => 'Unable to retrieve url'], 409);
        }

        return $this->json(['url' => $url]);

    }

    public function remove(Request $request, Response $response, array $args): Response
    {

        $validator = new \App\Helpers\Validator();

        $data = $request->getParsedBody();

        $rules = [

            'key' => 'required|exists:uploads,key',
        ];
        $messages = [

        ];

        $validationResult = $validator->make($data, $rules, $messages);

        if ($validationResult !== true) {

            return $this->json(['errors' => $validationResult], 409);
        }

        $validData = $validator->validData;

        $user_id = $request->getAttribute('user_id');

        $count = $this->db->count('uploads', ['key' => $validData->key, 'user_id' => $user_id]);

        if (! $count) {

            return $this->json(['error' => 'Invalid authorization to delete'], 422);
        }

        $s3 = new \App\Helpers\S3;

        if (! $s3->doesObjectExist($validData->key)) {

            return $this->json(['error' => 'Invalid file to delete'], 422);
        }

        $args = [
            'Key' => $validData->key, // Folder + filename in S3
        ];

        $result = $s3->delete($args);

        if (empty($result)) {

            return $this->json(['error' => 'Unable to remove file']);
        }

        $this->db->update('uploads', ['user_id' => -1], ['key' => $validData->key]);

        return $this->json(['message' => 'Removed file successfully']);

    }
}
