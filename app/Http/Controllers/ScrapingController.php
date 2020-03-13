<?php
namespace App\Http\Controllers;
 
 use Illuminate\Http\Request;
 use App\Http\Controllers\Controller;
 
 // Goutte ライブラリの読み込み
 use Goutte\Client;
 
 class ScrapingController extends Controller
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
        $requests->flash();
        $client = new Client();
        $words = [];
        if(strpos($requests->keyword,',') !== false){
            $words = explode(',',$requests->keyword); //['グローバルWifi','グローバルWiFi','グローバルwifi','グローバルWIFI','GLOBAL WIFI','GLOBAL WiFi','GLOBAL Wifi', 'GLOBAL wifi','Global WIFI', 'Global WiFi','Global Wifi', 'Global wifi'];
        }
        else{
            $words[0] = $requests->keyword;
        }
        
        //  // Https 関連でエラーが発生する場合があるので、チェックしないように設定
        $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
        $client->setClient($guzzleClient);

        $result = [];
        // $urls = $requests->urls;
            // echo $requests->urls;
        $array = explode("\n", $requests->urls); // とりあえず行に分割
        $array = array_map('trim', $array); // 各行にtrim()をかける
        $array = array_filter($array, 'strlen'); // 文字数が0の行を取り除く
        $array = array_values($array); 
        // var_dump($array);
        foreach ($array  as $u) {

            $crawler = $client->request('GET', $u);

            $crawler->filter('body')->each(function($node) use(&$result,$u,$words) {
                foreach( $words as $w ){
                    if(strpos($node->text(),$w) !== false){
                        array_push($result, [$u,$w] );
                    }
                }
            });
        }
        return view('scraping.execute', compact('result'));

    }
    public function pickUpUpls()
    {
    //    $requests->flash();
    //    $client = new Client();
       
    //    //  // Https 関連でエラーが発生する場合があるので、チェックしないように設定
    //    $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
    //    $client->setClient($guzzleClient);

    //    $result = [];
    //    // $urls = $requests->urls;
    //        // echo $requests->urls;
    //    $array = explode("\n", $requests->urls); // とりあえず行に分割
    //    $array = array_map('trim', $array); // 各行にtrim()をかける
    //    $array = array_filter($array, 'strlen'); // 文字数が0の行を取り除く
    //    $array = array_values($array); 
    echo '<pre>';
    $array =  json_decode( $this->getHrefUrl('http://www.tokoton-navi.com/knowledge_1.html'),1);
    var_dump($array);
    $i = 0;
    foreach($array as $a){
        $array_1[$i] =  json_decode( $this->getHrefUrl($a),1);
        $i++;
    }
    var_dump($array_1);
    //    foreach ($array  as $u) {

    //        $crawler = $client->request('GET', $u);

    //        $crawler->filter('body')->each(function($node) use(&$result,$u,$words) {
    //            foreach( $words as $w ){
    //                if(strpos($node->text(),$w) !== false){
    //                    array_push($result, [$u,$w] );
    //                }
    //            }
    //        });
    //    }
    //    return view('scraping.execute', compact('result'));

   }
    private function getHrefUrl($url){
        $result=array();
        $x = 0;

        $client = new Client();
        $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
        $client->setClient($guzzleClient);

        $crawler_site = $client->request('GET', $url);
        
        $crawler_site->filter('a')->each(function($n) use(&$result, $url ,&$x ){
                //var_dump($n->attr('href')) ;
                if( !in_array( $url , $result) ){
                    if(substr($n->attr('href'), 0, 1) == '/'){
                        array_push( $result , $url .  $n->attr('href'));
                        // echo $i++;

                    }elseif(substr($n->attr('href'), 0, 1) != '#' && ($n->attr('href') != 'javascript:void(0);') && ($n->attr('href') != '') ) {

                        array_push( $result , $n->attr('href'));
                    }
                }
                \Log::info($n->attr('href'));
                \Log::info($x);
                $x++;
        });
        return json_encode( $result );

    }


    //     // Google 検索 URL フォーマット
    //      $url_format = 'https://www.google.co.jp/search?q=%query%&num=%num%';
 
    //      // キーワード
    //      $keyword = 'カレンダー 人気';
 
    //      // キーワードのURLエンコード、SERPs の取得件数をセット
    //      $replace = [urlencode($keyword), 100];
    //      $search = ['%query%', '%num%'];
         
    //      // 実際にアクセスする URL
    //      $url = str_replace($search, $replace, $url_format);
 
    //      // Goutte ライブラリの事前準備
    //      $client = new Client();
 
    //      // Https 関連でエラーが発生する場合があるので、チェックしないように設定
    //      $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
    //     $client->setClient($guzzleClient);

    //     $result = [];

    //     // 検索結果の取得
    //     $crawler = $client->request('GET', $url);
    //     //var_dump($crawler->html());
    //     $crawler->filter('div.g')->each(function($node) use(&$result) {
    //         if (count($node->filter('a')) !== 0 && count($node->filter('h3')) !== 0) {
    //             $href = $node->filter('a')->attr('href');
    //             if (preg_match('/url\?/', $href)) {
    //                 $info = [];
    //                 $info['title'] = $node->filter('h3')->text();

    //                 preg_match('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@=+$,%#]+)', $href, $match);
    //                 $info['url'] = urldecode($match[0]);
    //                 $result[] = $info;
                    
    //             }
    //         }
    //     });
    // dd($result);

    
    
}