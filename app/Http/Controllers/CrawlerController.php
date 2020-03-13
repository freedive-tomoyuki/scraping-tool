<?php
namespace App\Http\Controllers;
 
 use Illuminate\Http\Request;
 use App\Http\Controllers\Controller;
 
 // Goutte ライブラリの読み込み
 use Goutte\Client;
 
class CrawlerController extends Controller
{
 
     /**
      * Display a listing of the resource.
      *
      * @return \Illuminate\Http\Response
      */
     public function index(Request $request)
     {
         // Google 検索 URL フォーマット
         $url_format = 'https://www.google.co.jp/search?q=%query%&num=%num%&';
 
         // キーワード
         $keyword = $request->input('q');//'引越し　比較';
         $result = [];
         // キーワードのURLエンコード、SERPs の取得件数をセット
         $replace = [urlencode($keyword), 10];
         $search = ['%query%', '%num%'];
         
         // 実際にアクセスする URL
         $url = str_replace($search, $replace, $url_format);
 
         // Goutte ライブラリの事前準備
         $client = new Client();
 
         // Https 関連でエラーが発生する場合があるので、チェックしないように設定
        $guzzleClient = new \GuzzleHttp\Client(['verify' => false]);
        $client->setClient($guzzleClient);


        if($keyword != '' )
        {
            // 検索結果の取得
            $crawler = $client->request('GET', $url);
            // echo $crawler->html();
            $urls = array();
            $titiles = array();
            $x = 0;

            $crawler->filter('.ZINbbc > div > a.C8nzq')->each(function($node) use(&$urls,&$x ) {
                // if (count($node->filter('a')) !== 0 && count($node->filter('h3')) !== 0) {
                //表示URL取得
                $url = $node->filter('.qzEoUe')->text();
                //タイトル取得
                $titile = $node->filter('.MUxGbd')->text();
                //     if (preg_match('/url\?/', $href)) {
                //         $info = [];
                //         $info['title'] = $node->filter('h3')->text();

                //         preg_match('(https?://[-_.!~*\'()a-zA-Z0-9;/?:@=+$,%#]+)', $href, $match);
                //         $info['url'] = urldecode($match[0]);
                //         $result[] = $info;
                // echo $node -> html();
                //     }
                // }
                $url = explode("/", $url);
                // // var_dump($url[0]);
                // array_push($urls ,'https://'.$url[0];
                // array_push($titiles ,$node);
                $urls[$x]['url'] = 'https://'.$url[0] ;
                $urls[$x]['title'] = $titile ;
                $x++;
            });
            // $ahref = array();
            // dd($urls);
            // $i = 0;
            foreach($urls as $u){
                // echo $u;
                $crawler_site = $client->request('GET', $u['url']);
                // echo $crawler_site->html();
                // echo $i;
                $crawler_site->filter('a')->each(function($n) use(&$result, $u, &$i) {
                    //var_dump($n->attr('href')) ;
                    if(substr($n->attr('href'), 0, 1) == '/'){
                        $result[$i]['domain'] = $u['url'] ;
                        $result[$i]['title'] = $u['title'] ;
                        $result[$i]['url'] = $u['url'] .  $n->attr('href');
                        // echo $i++;
                    }elseif(substr($n->attr('href'), 0, 1) != '#' && ($n->attr('href') != 'javascript:void(0);') && ($n->attr('href') != '') ) {
                        $result[$i]['domain'] = $u['url'] ;
                        $result[$i]['title'] = $u['title'] ;
                        $result[$i]['url'] =  $n->attr('href');
                        // echo $i++;
                    }
                    $i++;
                });
            }
            // dd($result);
        }
        // echo '<pre>';
        // var_dump($ahref) ;
        return view('scraping.show',compact('result','keyword'));

    }

}
