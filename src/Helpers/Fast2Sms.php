<?php
namespace App\Helpers;

class Fast2Sms
{

    private $key;

    public function __construct()
    {
        $this->key = $_ENV['FAST2SMS_KEY'];

    }

    public function send($mobile, $otp)
    {

        $client = new \GuzzleHttp\Client();

        $response = $client->get('https://www.fast2sms.com/dev/bulkV2', [
            'headers' => [
                'Authorization' => $this->key,
            ],
            'query'   => [
                'variables_values' => $otp,
                'route'            => 'otp',
                'numbers'          => $mobile,
            ],
        ]);

        $result = json_decode($response->getBody()->getContents(), true);

        return $result;
    }

    public function send_post($mobile, $otp)
    {

        $client = new \GuzzleHttp\Client();

        $url     = 'https://www.fast2sms.com/dev/otp/send';
        $headers = [
            'Authorization' => $this->key,
            'accept'        => 'application/json',
            'content-type'  => 'application/json',
        ];
        $body = [
            'mobile' => $mobile,
            'otp'    => $otp,
            'otp_id' => uniqid('ezpze'),
        ];
        $response = $client->request('POST', $url, [
            'body'    => json_encode($body),
            'headers' => $headers,
        ]);

        return true;
    }
}
