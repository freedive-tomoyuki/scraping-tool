<?php
namespace App\Http\Controllers;
 
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
 
 // Goutte ライブラリの読み込み
use Goutte\Client;
use DB;

class ImotoController extends Controller
{
 
     /**
      * Display a listing of the resource.
      *
      * @return \Illuminate\Http\Response
      */
     public function index()
     {

        //
        //$imotoUrl = DB::table('imoto_checker_20190911_results')
        //    ->select('imoto_checker_20190911.id','imoto_checker_20190911.url')
        //    ->join('imoto_checker_20190911','imoto_checker_20190911_results.imoto_checker_20190911_id','=','imoto_checker_20190911.id' )
            //->where('imoto_checker_20190911_results.id' , '>=' , 200 )
            //->where('imoto_checker_20190911_results.id' , '<' , 400 )
        //   ->get();

        // $imotoUrl = DB::table('imoto_checker_20190911')
        //     ->select('id','url')
        //     ->where('id','>=', 2867 )
        //     //->where('id','<=', 4000 )
        //     //->where('flag','!=', 2 )
        //     ->get();
        
        $url = DB::table('grobal_wifi_checker')
            ->select('id','url')
            // ->where('id','<=',  )
            //->where('id','<=', 4000 )
            ->where('flag','!=', 1 )
            ->get();

        // echo "<pre>";
        // var_dump($imotoUrl);
        // echo "</pre>";
         // Google 検索 URL フォーマット
         //$url_format = 'https://motimono-list.com/event/kaigai/creditcard/tesuryou/';
 
         // キーワード
         //$keyword = 'カレンダー 人気';
 
         // キーワードのURLエンコード、SERPs の取得件数をセット
         // $replace = [urlencode($keyword), 100];
         // $search = ['%query%'), '%num%'];
         
         // // 実際にアクセスする URL
         // $url = str_replace($search, $replace, $url_format);
 
         // Goutte ライブラリの事前準備
        $client = new Client();
        $words = ['グローバルWifi','グローバルWiFi','グローバルwifi','グローバルWIFI','GLOBAL WIFI','GLOBAL WiFi','GLOBAL Wifi', 'GLOBAL wifi','Global WIFI', 'Global WiFi','Global Wifi', 'Global wifi'];
        //  // Https 関連でエラーが発生する場合があるので、チェックしないように設定
        $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
        $client->setClient($guzzleClient);

        $result = [];
        foreach ($url  as $key => $value) {
            // 検索結果の取得
            // echo '<pre>';
            // var_dump($value->url);
            // echo '</pre>';
            $breadcrumb_1 = '';
            $breadcrumb_2 = '';
            $breadcrumb_3 = '';

            $crawler = $client->request('GET', $value->url);

            $crawler->filter('body')->each(function($node) use(&$result,$value,$words) {
                
                DB::table('grobal_wifi_checker')->where('id',$value->id)->update(['flag' => 1]);
                foreach( $words as $w ){
                    if(strpos($node->text(),$w) !== false){
                        echo '<pre>';
                        echo "含まれるURL：".$value->url;
                        echo '</pre>';

                        DB::table('grobal_wifi_checker_results')->insert([ 'grobal_wifi_checker_id' => $value->id , 'word'=> $w ]);
                    }
                }
            });

            /*$crawler = $client->request('GET', $value->url);
            //$crawler->filter('body')->each(function($node) use(&$result,$value) {
                echo '<pre>';
                if (count($crawler->filter('#breadcrumb > ul > li:nth-child(2)')) !== 0 ){
                    echo $breadcrumb_1 = $crawler->filter('#breadcrumb > ul > li:nth-child(2)')->text();
                }
                if (count($crawler->filter('#breadcrumb > ul > li:nth-child(3)')) !== 0 ){
                    echo $breadcrumb_2 = $crawler->filter('#breadcrumb > ul > li:nth-child(3)')->text();
                    //DB::table('imoto_checker_20190911_results')->insert([ 'breadcrumb_3' => $value->id ]);
                }
                if (count($crawler->filter('#breadcrumb > ul > li:nth-child(4)')) !== 0 ){
                    echo $breadcrumb_3 = $crawler->filter('#breadcrumb > ul > li:nth-child(4)')->text();
                    //DB::table('imoto_checker_20190911_results')->insert([ 'breadcrumb_3' => $value->id ]);
                }

                //echo $node->text();
                echo '</pre>';
                    
                DB::table('imoto_checker_20190911_result_countries')->insert([ 
                        'imoto_checker_20190911_id'=> $value->id ,
                        'breadcrumb_1' => $breadcrumb_1 ,
                        'breadcrumb_2' => $breadcrumb_2 ,
                        'breadcrumb_3' => $breadcrumb_3 ]);

                //echo $node->text();
                //if(strpos($node->text(),'イモト') !== false){
                //    echo "含まれるURL：".$value->url;
                //    DB::table('imoto_checker_20190911_results')->insert([ 'imoto_checker_20190911_id' => $value->id ]);
                //}
                //
                // if (count($node->filter('a')) !== 0 && count($node->filter('h3')) !== 0) {
                //     $href = $node->filter('a')->attr('href');
                //     if (preg_match('/url\?/'), $href)) {
                //         $info = [];
                //         $info['title'] = $node->filter('h3')->text();

                //         preg_match('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@=+$,%#]+)'), $href, $match);
                //         $info[url] = urldecode($match[0]);
                //         $result[] = $info;
                        
                //     }
                // }
            //});
        */
        }
        
        //dd($result);

    }

}
