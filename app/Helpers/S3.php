<?php

namespace App\Helpers;

use App\Helpers\Logger;
use Aws\S3\S3Client;

class S3
{

    private $s3Client;

    private $bucketName = 'mybucket';

    public function __construct()
    {

        $this->s3Client = new S3Client([
            'region' => $_ENV['AWS_REGION'],
            'credentials' => [
                'key' => $_ENV['AWS_KEY'],
                'secret' => $_ENV['AWS_SECRET'],
            ],
        ]);
    }

    public function put($args)
    {

        $logger = new Logger();


        try {

            $args['Bucket'] = $this->bucketName;

            $this->s3Client->putObject($args);

            return true;
        } catch (Exception $exception) {
        $key = !empty($args['Key'])?$args['Key']:'filepath';

            $logger->error("Failed to upload $key with error: ", $exception->getMessage());
            return false;
        }
    }

    public function getUrl($key)
    {

        $logger = new Logger();

        try {

           return $this->s3Client->getObjectUrl($this->bucketName, $key);

        } catch (Exception $exception) {
            $logger->error("Failed to get $key with error: ", $exception->getMessage());
            return false;
        }
    }

}
