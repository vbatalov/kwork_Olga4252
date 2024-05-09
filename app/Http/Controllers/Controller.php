<?php

namespace App\Http\Controllers;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\TransferStats;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Http;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function getIp()
    {
        foreach (array('HTTP_CLIENT_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_FORWARDED', 'HTTP_X_CLUSTER_CLIENT_IP', 'HTTP_FORWARDED_FOR', 'HTTP_FORWARDED', 'REMOTE_ADDR') as $key) {
            if (array_key_exists($key, $_SERVER) === true) {
                foreach (explode(',', $_SERVER[$key]) as $ip) {
                    $ip = trim($ip); // just to be safe
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) !== false) {
                        return $ip;
                    }
                }
            }
        }
        return request()->ip(); // it will return the server IP if the client IP is not found using this method.
    }

    public function ip(\Request $request)
    {
        \Log::alert($this->getIp());
        return $this->getIp();
    }

    /**
     * @throws GuzzleException
     */
    public function http()
    {
        $proxies = [
            "http" => "http://8A8cj7:QTbDgf@217.29.53.85:13700"
        ];
        $stats1 = "";
        $client = new Client([
            RequestOptions::PROXY => $proxies,
//            RequestOptions::VERIFY => true, # disable SSL certificate validation
            RequestOptions::TIMEOUT => 30, # timeout of 30 seconds
            RequestOptions::ON_STATS =>
                function (TransferStats $stats) {
                    echo $stats->getEffectiveUri() . "\n";
                    echo $stats->getTransferTime() . "\n";
                    var_dump($stats->getHandlerStats());
                }

        ]);

        $response = $client->get("https://9f69-176-209-164-148.ngrok-free.app/ip");

//        $client = new Client();
//
//        $client->request('POST', 'https://фгислк.рф',
//            [
//                'proxy' => 'http://8A8cj7:QTbDgf@217.29.53.85:13700',
//                "headers" => [
//                    'User-Agent' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2.1 Safari/605.1.15',
//                    "Accept" => 'text/html,application/xhtml+xml,application/xml;q=0.9,*/*;q=0.8',
//                ],
//                "cookies" => [
//
//                ]
//            ]);

        dd($stats1);
        dd($response->getHeaders());
    }


}
