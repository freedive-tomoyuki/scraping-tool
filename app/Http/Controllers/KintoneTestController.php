<?php
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
 
use App\Post;
use App\Http\Requests\PostRequest;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\RequestInterface;
 
 class KintoneTestController extends Controller
 {
    public function index(){
        return view('scraping.execute');
    }
     /**
      * Display a listing of the resource.
      *
      * @return \Illuminate\Http\Response
      */
     public function run(Request $requests)
     {
        $subDomain = env('KINTONE_SUBDOMAIN');//'http://example.com';
        // echo env('KINTONE_LOGIN');
        $base_url = "http://" . $subDomain . ".cybozu.com/k/v1/record.json";
                // リクエストヘッダ
                $headers = [

                    // 'host' => 'freedive.cybozu.com',
                    'x-cybozu-api-token' => '5kLjSNJTr8oRozz14iEBhDPSc0jFGbCXib1JBYsx',
                    'x-cybozu-authorization' => base64_encode(env('KINTONE_LOGIN') . ':' . env('KINTONE_PASSWORD')),
                    ' Content-Type' => 'application/json',
                ];
        $client = new Client();
        
        $appId = 10;
        // $rsc = fopen("out.html", "w");
        // $header = array(
        //     "Host: " . $subDomain . ".cybozu.com:443",
        //     "Content-Type: application/json",
        //     "X-Cybozu-Authorization: " . base64_encode(env('KINTONE_LOGIN') . ':' . env('KINTONE_PASSWORD')),
        //     "X-Cybozu-API-Token: 5kLjSNJTr8oRozz14iEBhDPSc0jFGbCXib1JBYsx"

        // );

        $body = [
            'app'=> 10,
            'record' => [
                'id' => [
                    'value' => '100'
                ],
                'name' => [
                    'value' => 'test'
                ],
                'plan' => [
                    'value' => 'test'
                ],
                'price'=> [
                    'value' => '1000'
                ],
                'agreement_date'=> [
                    'value' => '2020-03-28'
                ],
                'device'=> [
                    'value' => 'test'
                ],
                'coverage_option'=> [
                    'value' => 'あり'
                ],
                'name_kana'=> [
                    'value' => 'test'
                ],
                'phone_number'=> [
                    'value' => '080-2222-2222'
                ]
               
            ]
        ];
        try {
            echo '<pre>';
             $response = $client->request('POST',$base_url, [
                'headers' => $headers,
                // 'form_params' => $body,
                'json' => json_encode($body),
                //'{"app":10,"record":{"id":{"value":100},"name":{"value":"test"},"plan":{"value":"test"},"price":{"value":1000},"agreement_date":{"value":"2020-03-26"},"device":{"value":"test"},"coverage_option":{"value":"\\u3042\\u308a"},"name_kana":{"value":"test"},"phone_number":{"value":"080-2222-2222"}}}', // ここにぶち込むと勝手にjsonにしてくれる
                // 'http_errors' => false,
                // 'handler' => $stack,
                // 'debug'=>true,
                // 'otheroptions'=>array()
                // 'query' => $body,
                // 'on_stats' => function (\GuzzleHttp\TransferStats $stats) use (&$url) {
                //     echo $url = $stats->getEffectiveUri();
                // }
            ] )->getBody()->getContents();
            echo $url;
        } catch (ClientException $e) {
            // echo $response = $e->getResponse();
        }

    }
    
}