<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Laravel\Dusk\Browser;
use Symfony\Component\DomCrawler\Crawler;

//use Revolution\Salvager\Facades\Salvager;

use Revolution\Salvager\Client;
use Revolution\Salvager\Drivers\Chrome;

use App\Dailydata;
use App\Product;
use App\Dailysite;

header('Content-Type: text/html; charset=utf-8');

class ChromeheadlessController extends Controller
{

    public function index()
    {
        return view('cralwerdaily');
    }
    public function show_test()
    {
      $datas = \App\Product::all()->where('id','6');
      //var_dump($datas->asp) ;
      foreach($datas as $data)
      {
          //データにnullの値があるためif文入れる（本来はいらない）
          //if(!empty($data->asp->cv)) 
              //部署名取得
              echo $data->asp->login_url;
              //echo $data->asp->imp." ".$data->asp->click."<br>";
      }
    }
    public function save_daily($data){
        $data_array = json_decode(json_encode(json_decode($data)), True );
/*
        echo gettype($data_array);
        var_dump($data_array);
        
        var_dump($data_array[0]['cv']);
        var_dump($data_array[0]['click']);
        var_dump($data_array[0]['imp']);
*/
        $cv = intval($data_array[0]['cv']); 
        $click = intval($data_array[0]['click']);
        $imp = intval($data_array[0]['imp']);

        $crv = ($cv != 0)? ( $cv / $click ) * 100 : 0 ;
        $ctv = ($click != 0)? ( $click / $imp ) * 100 : 0 ;

        Dailydata::create(
            [
            'imp' => $imp,
            'click' => $click,
            'cv' => $cv,
            'cvr' => round($crv,2),
            'ctr' => round($ctv,2),
            'active' => $data_array[0]['active'],
            'partnership' => $data_array[0]['partnership'],
            'asp_id' => $data_array[0]['asp'],
            'product_id' => $data_array[0]['product']
            ]
        );

    }
    public function save_site($data){
        $month = date('m');
        $date = date('d');
        
        $data_array = json_decode(json_encode(json_decode($data)), True );

        //echo gettype($data_array);

        //var_dump($data_array);

        //for($i=0 ; $i <= count($data_array[0]) ; $i++){
        foreach($data_array as $data ){

/*          echo '<pre>';
            var_dump($data);
          echo '</pre>';
*/
            $cv = (intval($data['cv'])) ? intval($data['cv']) : 0 ; 
            $click = (intval($data['click'])) ? intval($data['click']) : 0 ;
            $imp = (intval($data['imp'])) ? intval($data['imp']) : 0 ;

            $cvr = ($cv == 0 || $click ==0 )? 0 : ( $cv / $click ) * 100 ;
            $ctr = ($click == 0|| $imp ==0 )? 0 : ( $click / $imp ) * 100 ;

            Dailysite::create(
                [
                  'media_id' => $data['media_id'],
                  'site_name' => $data['site_name'],
                  'imp' => $imp,
                  'click' => $click,
                  'cv' => $cv,
                  'cvr' => round($cvr, 2),
                  'ctr' => round($ctr, 2),
                  'product_id' => $data['product'],
                  'month' => $month,
                  'day' => $date
                ]
            );

        }

    }
   public function a8(){//OK

            Browser::macro('crawler', function () {
                return new Crawler($this->driver->getPageSource() ?? '', $this->driver->getCurrentURL() ?? '');
            });

            $options = [
                '--window-size=1920,1080',
                '--start-maximized',
                //'--headless',
                '--lang=ja_JP',
            ];

              $client = new Client(new Chrome($options));

              $client->browse(function (Browser $browser) use (&$crawler) {

                $product_infos = \App\Product::all()->where('id','1');
                
                foreach ($product_infos as $product_info){

                  $crawler = $browser->visit($product_info->asp->login_url)
                                  ->type($product_info->login_key , $product_info->login_value)
                                  ->type($product_info->password_key , $product_info->password_value )
                                  ->click($product_info->asp->login_selector) 
                                  ->visit($product_info->asp->lp1_url.$product_info->asp_product_id) 
                                  ->crawler();

                  $crawler_for_active = $browser->visit('https://adv.a8.net/a8v2/ecReportAction.do')
                                  ->select('#reportOutAction > table > tbody > tr:nth-child(2) > td > select','23')
                                  ->radio('insId',$product_info->asp_product_id)
                                  ->click('#reportOutAction > input[type="image"]:nth-child(3)')
                                  ->crawler();

                  $crawler_for_site = $browser->visit('https://adv.a8.net/a8v2/ecReportAction.do')
                                  ->select('#reportOutAction > table > tbody > tr:nth-child(2) > td > select','11')
                                  ->radio('insId',$product_info->asp_product_id)
                                  ->click('#reportOutAction > input[type="image"]:nth-child(3)')
                                  ->crawler();

                                $xpaths = array (
                                  //'month' => '//*[@id="element"]/tbody/tr[1]/td[1]',
                                    'imp' => $product_info->asp->daily_imp_selector  ,
                                    'click' => $product_info->asp->daily_click_selector,
                                    'cv' => $product_info->asp->daily_cv_selector,
                                    'partnership' => $product_info->asp->daily_partnership_selector,
                                    //'active' => $product_info->asp->daily_active_selector ,
                                );
                                $xpaths_active = array (
                                    'active' => "#ReportList > tbody > tr:nth-child(1) > td:nth-child(4)"
                                );

                                $a8data = $crawler->each(function (Crawler $node)use ( $xpaths ,$product_info){
                                  $data = array();
                                  $data['asp'] = $product_info->asp_id;
                                  $data['product'] = $product_info->id;

                                  foreach($xpaths as $key => $value){
                                      //echo $value;
                                      $data[$key] = trim($node->filter($value)->text());
                                      //echo trim($node->filterXpath($value)->text());

                                  }
                                  return $data;

                                });
                                $active_data = $crawler_for_active->each(function (Crawler $node)use ( $xpaths_active ){

                                  foreach($xpaths_active as $key => $value){
                                      $data[$key] = trim($node->filter($value)->text());
                                  }
                                  return $data;

                                });
                                
                                //var_dump($active_data);
                                  //アクティブ数　格納
                                  $a8data[0]['active'] = trim(preg_replace('/[^0-9]/', '', $active_data[0]["active"]));

                                  $count_selector = '#contents1clm > form:nth-child(6) > span.pagebanner';
                                  $count_data = intval(
                                                    trim(
                                                      preg_replace(
                                                        '/[^0-9]/', '', substr($crawler_for_site->filter($count_selector)->text(), 0, 5)
                                                      )
                                                    )
                                                );

                                  //$count_first = ($count_data > 500)? 500 : $count_data ;
                                  $page_count = ceil($count_data/500) ;
                                  //echo "page_count>".$page_count;
                                  /*echo "first_count>".$first_count ;

                                  for( $i = 1 ; $i <= $first_count ; $i++){
                                    $xpath_for_site = array(
                                        'media_id'=> '#ReportList > tbody > tr:nth-child('.$i.') > td:nth-child(2) > a',
                                        'site_name'=> '#ReportList > tbody > tr:nth-child('.$i.') > td:nth-child(4)',
                                        'imp'=> '#ReportList > tbody > tr:nth-child('.$i.') > td:nth-child(5)',
                                        'click'=> '#ReportList > tbody > tr:nth-child('.$i.') > td:nth-child(6)',
                                        'cv'=> '#ReportList > tbody > tr:nth-child('.$i.') > td:nth-child(10)',
                                    );
                                    $data[$i]['product'] = $product_info->id;

                                    foreach($xpath_for_site as $key => $value){
                                            $data[$i][$key] = trim($crawler_for_site->filter($value)->text());
                                    }
                                  
                                  };*/

                                  //$page = 1;

                                  

                              //while( $crawler_for_site->filter('#contents1clm > form:nth-child(6) > span.pagelinks > a:nth-child(4)')->count() != 0 ){

                              for($page=0 ; $page<$page_count ;$page++){
                                
                                $target_page = $page+1;
                                
                                $url = 
                                'https://adv.a8.net/a8v2/ecAsRankingReportAction.do?reportType=11&insId='.$product_info->asp_product_id.'&asmstId=&termType=1&d-2037996-p='.$target_page.'&multiSelectFlg=0';

                                  $crawler_for_site = $browser-> visit($url)
                                                      -> crawler();

                                  //echo "count_data＞".intval($count_data) ."＜count_data<br>";
                                  $count_deff = intval($count_data)-(500*$page);

                                  //echo "count_deff1＞".$count_deff."＜count_deff1<br>" ;

                                  $count_deff = (intval($count_deff) > 500)? 500 : intval($count_deff);
                                  
                                  //echo "count_deff2＞".$count_deff."＜count_deff2<br>" ;


                                  for( $i = 1 ; $i <= $count_deff ; $i++){
                                    $count = $i+(500*$page);
                                    $xpath_for_site = array(
                                        'media_id'=> '#ReportList > tbody > tr:nth-child('.$i.') > td:nth-child(2) > a',
                                        'site_name'=> '#ReportList > tbody > tr:nth-child('.$i.') > td:nth-child(4)',
                                        'imp'=> '#ReportList > tbody > tr:nth-child('.$i.') > td:nth-child(5)',
                                        'click'=> '#ReportList > tbody > tr:nth-child('.$i.') > td:nth-child(6)',
                                        'cv'=> '#ReportList > tbody > tr:nth-child('.$i.') > td:nth-child(10)',
                                    );
                                    
                                    $data[$count]['product'] = $product_info->id;

                                    foreach($xpath_for_site as $key => $value){
                                            $data[$count][$key] = trim($crawler_for_site->filter($value)->text());
                                    }
                                  
                                  };


                                  //$page = $page + 1;
                              }
                                //var_dump($data);

                                $this->save_daily(json_encode($a8data));
                                $this->save_site(json_encode($data));

                }
            });

    }
   public function accesstrade(){//OK

            Browser::macro('crawler', function () {
                return new Crawler($this->driver->getPageSource() ?? '', $this->driver->getCurrentURL() ?? '');
            });

            $options = [
                '--window-size=1920,1080',
                '--start-maximized',
                //'--headless',
            ];
            

            $client = new Client(new Chrome($options));

            $client->browse(function (Browser $browser) use (&$crawler) {

              $product_infos = \App\Product::all()->where('id','2');
                
                foreach ($product_infos as $product_info){
                  // /var_dump($product_info->asp);
                  $crawler = $browser->visit($product_info->asp->login_url)
                                  ->type($product_info->login_key , $product_info->login_value)
                                  ->type($product_info->password_key , $product_info->password_value )

                                  ->click($product_info->asp->login_selector)
                                  ->visit($product_info->asp->lp1_url.$product_info->asp_product_id )
                                  ->crawler();


                                $xpaths = array (
                                    //'month' => '//*[@id="element"]/tbody/tr[1]/td[1]',
                                      'imp' => $product_info->asp->daily_imp_selector  ,
                                      'click' => $product_info->asp->daily_click_selector,
                                      'cv' => $product_info->asp->daily_cv_selector,
                                      'partnership' => $product_info->asp->daily_partnership_selector,
                                      'active' => $product_info->asp->daily_active_selector ,
                                );
                              
                                //var_dump($xpaths);
                                //$crawler->each(function (Crawler $node) use ( $xpaths ){
                                
                                $atdata = $crawler->each(function (Crawler $node)use ( $xpaths ,$product_info){
                                
                                      $data = array();
                                      $data['asp'] = $product_info->asp_id;
                                      $data['product'] = $product_info->id;

                                      foreach($xpaths as $key => $value){
                                        
                                          if($key == 'active'){
                                            $active = explode("/", $node->filter($value)->text());
                                            $data[$key] = trim($active[0]);

                                          }elseif($key == 'partnership'){

                                            $data[$key] = trim(preg_replace('/[^0-9]/', '', $node->filter($value)->text()));

                                          }else{

                                            $data[$key] = trim(preg_replace('/[^0-9]/', '', $node->filter($value)->text()));

                                          }
                                          

                                      }
                                      //var_dump($data);
                                  return $data;

                                });

                                $array_site = array();
                                $crawler_for_site = $browser->visit("https://merchant.accesstrade.net/mapi/program/".$product_info->asp_product_id."/report/partner/monthly/occurred?targetFrom=".date('Y-m-01')."&targetTo=".date('Y-m-d')."&pointbackSiteFlagList=0,1")->crawler();


                                $array_site=$crawler_for_site->text();

                                $array_site = json_decode($array_site,true);
                                
                                $array_sites = $array_site["report"];

                                $x = 0; 

                                foreach($array_sites as $site){
                                  $data[$x]['product'] = $product_info->id ;
                                  $data[$x]['media_id'] = $site["partnerSiteId"] ;
                                  $data[$x]['site_name'] = $site["partnerSiteName"] ;
                                  $data[$x]['imp'] = $site["impressionCount"] ;
                                  $data[$x]['click'] = $site["clickCount"] ;
                                  $data[$x]['cv'] = $site["actionCount"] ;
                                
                                  $x++;
                                
                                }
                                //var_dump($data);
                                   
                            $this->save_site(json_encode($data));
                            $this->save_daily(json_encode($atdata));
                }
                                
            });

    }
   public function vc(){

            Browser::macro('crawler', function () {
                return new Crawler($this->driver->getPageSource() ?? '', $this->driver->getCurrentURL() ?? '');
            });

            $options = [
                '--window-size=1920,1080',
                '--start-maximized',
               //'--headless',
            ];

            $client = new Client(new Chrome($options));

            $client->browse(function (Browser $browser) use (&$crawler) {
            
              $product_infos = \App\Product::all()->where('id','3');
                
              foreach ($product_infos as $product_info){

              $crawler = $browser->visit($product_info->asp->login_url)
                                  ->type($product_info->login_key , $product_info->login_value)
                                  ->type($product_info->password_key , $product_info->password_value )

                                  ->click($product_info->asp->login_selector)
                                  ->visit($product_info->asp->lp1_url )
                                  ->crawler();
                      
                              $crawler2 = $browser->visit($product_info->asp->lp2_url)
                                  ->crawler();
                      
                              $xpaths_crawler = array (
                                    'imp' => $product_info->asp->daily_imp_selector  ,
                                    'click' => $product_info->asp->daily_click_selector,
                                    'cv' => $product_info->asp->daily_cv_selector,
                                    'partnership' => $product_info->asp->daily_partnership_selector,
                              );

                              $xpaths_crawler2 = array (
                                    'active' => $product_info->asp->daily_active_selector ,
                              );



                              $vcdata = $crawler->each(function (Crawler $node) use ( $xpaths_crawler ,$product_info){
                                $data = array();
                                
                                      $data['asp'] = $product_info->asp_id;
                                      $data['product'] = $product_info->id;

                                //echo $node->html();
                                foreach($xpaths_crawler as $key => $value){
                                      $data[$key] = array();
                                      $data[$key] = trim(preg_replace('/[^0-9]/', '', $node->filter($value)->text()));
                                      //echo $node->filter($value)->text();
                                      //echo "\n";
                                }
                               return $data;
                              });
                              $active_data = $crawler2->each(function (Crawler $node) use ( $xpaths_crawler2 ){
                                foreach($xpaths_crawler2 as $key => $value){
                                      $active = explode("/", $node->filter($value)->text());
                                      //echo $active[1];
                                }
                                return $active[1];
                              });
                              //アクティブ数　格納
                              $vcdata[0]['active'] = trim(preg_replace('/[^0-9]/', '', $active_data[0]));

                                //１ページ目クロール
                                  $pagination_page = $product_info->asp->lp2_url;
                                  $crawler_for_site = $browser->visit($pagination_page)->crawler();
                                  
                                  $count_page = ($vcdata[0]['active']>40)? ceil($vcdata[0]['active']/40) : 1 ;
                                  //echo "count_page＞＞".$count_page ;
                                 /* for( $i = 1 ; $i < 40 ; $i++){
                                    $data[$i]['product'] = $product_info->id;

                                    $xpath_for_site = array(
                                          'media_id'=>'#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(2)',
                                          'site_name'=>'#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(3) > a',
                                          'imp'=>'#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(7)',
                                          'click'=>'#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(8)',
                                          'cv'=>'#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(19)',
                                          'ctr'=>'#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(9)',
                                          'cvr'=>'#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(22)',
                                    );
                                        foreach($xpath_for_site as $key => $value){
                                          
                                          if( $key == 'site_name' ){
                                          
                                            $data[$i][$key] = trim($crawler_for_site->filter($value)->text());
                                          
                                          }else{
                                          
                                            $data[$i][$key] = trim(preg_replace('/[^0-9]/', '', $crawler_for_site->filter($value)->text()));
                                          
                                          }
                                        }
                                  };*/
                              //while( $crawler_for_site->filter('#all > ul:nth-child(2) > li.next')->count() != 0 ){

                              //$count = 0 ;
                              
                              for($page = 0 ; $page < $count_page ; $page++){
                                    $target_page = $page+1;
                                    
                                    $crawler_for_site = 
                                            $browser  //-> visit($product_info->asp->lp2_url)
                                                      //-> click('#all > ul:nth-child(2) > li.next')
                                                      -> visit('https://mer.valuecommerce.ne.jp/affiliate_analysis/?condition%5BfromYear%5D='.date("Y").'&condition%5BfromMonth%5D='.date("n").'&condition%5BtoYear%5D='.date("Y").'&condition%5BtoMonth%5D='.date("n").'&condition%5BactiveFlag%5D=Y&allPage=1&notOmksPage=1&omksPage=1&pageType=all&page='.$target_page)
                                                      -> crawler();

                                  //$crawler_for_site = $browser->visit($pagination_page)->crawler();
                                  
                                  //最終ページのみ件数でカウント
                                  $crawler_count = ( $target_page == $count_page )? $vcdata[0]['active']-($page * 40) :40 ;
                                  
                                  //echo $target_page."ページ目のcrawler_count＞＞".$crawler_count."</br>" ;

                                  for($i=1 ; $i <= $crawler_count ; $i++){
                                    
                                    $count = ($page * 40) + $i;

                                    $data[$count]['product'] = $product_info->id;

                                    if($crawler_for_site->filter('#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(2)')->count() != 0){
                                      
                                      //echo $target_page."ページの i＞＞".$i."番目</br>" ;

                                        $xpath_for_site = array(
                                              'media_id'=>'#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(2)',
                                              'site_name'=>'#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(3) > a',
                                              'imp'=>'#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(7)',
                                              'click'=>'#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(8)',
                                              'cv'=>'#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(19)',

                                              //'ctr'=>'#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(9)',
                                              //'cvr'=>'#all > div.tablerline > table > tbody > tr:nth-child('.$i.') > td:nth-child(22)',
                                        );
                                    
                                        foreach($xpath_for_site as $key => $value){
                                          
                                          if( $key == 'site_name' ){
                                          
                                            $data[$count][$key] = trim($crawler_for_site->filter($value)->text());
                                          
                                          }else{
                                          
                                            $data[$count][$key] = trim(preg_replace('/[^0-9]/', '', $crawler_for_site->filter($value)->text()));
                                          
                                          }
                                        }
                                      }
                                  }

                              }
                              $this->save_daily(json_encode($vcdata));
                              $this->save_site(json_encode($data));

              }
            });
    }
  public function afb(){//OK

            Browser::macro('crawler', function () {
                return new Crawler($this->driver->getPageSource() ?? '', $this->driver->getCurrentURL() ?? '');
            });

            $options = [
                '--window-size=1920,1080',
                '--start-maximized',
                //'--headless',
                //'--disable-gpu',
                //'--no-sandbox'
            ];

            $client = new Client(new Chrome($options));

            $client->browse(function (Browser $browser) use (&$crawler) {
              
              $product_infos = \App\Product::all()->where('id','4');
                
              foreach ($product_infos as $product_info){

                  $crawler = $browser->visit($product_info->asp->login_url)
                                  
                                  ->type($product_info->login_key , $product_info->login_value)
                                  ->type($product_info->password_key , $product_info->password_value )
                                  ->click($product_info->asp->login_selector)
                                  
                                  ->visit($product_info->asp->lp1_url )
                                  ->radio('span_monthly','0mon')
                                  ->click('#report_form_1 > div > table > tbody > tr:nth-child(5) > td > p > label:nth-child(1)')
                                  ->click('#report_form_1 > div > table > tbody > tr:nth-child(5) > td > p > label:nth-child(2)')
                                  ->click('#report_form_1 > div > table > tbody > tr:nth-child(5) > td > p > label:nth-child(3)')
 
                                  ->click('#report_form_1 > div > div.btn_area.mt20 > ul.btn_list_01 > li > input')
                                  
                                  ->crawler();

                                  $crawler2 = $browser
                                  ->visit('https://client.afi-b.com/client/b/cl/main')
                                  ->crawler();
                              
                                $crawler3 = $browser
                                  ->visit($product_info->asp->lp3_url)
                                  //->radio('span','tm')
                                  ->click('#site_tab_bth')
                                  ->click('#report_form_4 > div > table > tbody > tr:nth-child(6) > td > p > label:nth-child(1)')
                                  ->click('#report_form_4 > div > table > tbody > tr:nth-child(6) > td > p > label:nth-child(2)')
                                  ->click('#report_form_4 > div > table > tbody > tr:nth-child(6) > td > p > label:nth-child(3)')
                                  ->click('#report_form_4 > div > div.btn_area.mt20 > ul.btn_list_01 > li > input')
                                  ->crawler();

                                
                                  
                              $xpaths_crawler = array (
                                    'imp' => $product_info->asp->daily_imp_selector  ,
                                    'click' => $product_info->asp->daily_click_selector,
                                    'cv' => $product_info->asp->daily_cv_selector,
                                    'price' => '#reportTable > tbody > tr > td:nth-child(9) > p',

                              );
                              $xpaths_crawler2 = array (
                                    'partnership' => '#main > div.wrap > div.section33 > div.section_inner.positionr.positionr > table > tbody > tr:nth-child(13) > td:nth-child(2)'
                              );
                              
                              $xpaths_crawler3 = array (
                                  'active' => $product_info->asp->daily_active_selector ,
                              );

                              $afbdata = $crawler->each(function (Crawler $node) use ( $xpaths_crawler ,$product_info){
                                
                                $data = array();
                                $data['asp'] = $product_info->asp_id;
                                $data['product'] = $product_info->id;


                                foreach($xpaths_crawler as $key => $value){

                                  $data[$key] = trim(preg_replace('/[^0-9]/', '', $node->filter($value)->text()));

                                }
                                //$data['cpa']= $this->cpa($data['cv'] ,$data['price'] , 3);

                                return $data;

                              });
                              $partnership = $crawler2->each(function (Crawler $node)use ( $xpaths_crawler2  ) {
                                //echo $node->html();
                                //echo $node->html();

                                foreach($xpaths_crawler2 as $key => $value){
                                      //$data[$key] = array();
                                      //$data[$key] = $node1->filter($value)->text();
                                  $partnership = trim(preg_replace('/[^0-9]/', '', $node->filter($value)->text()));
                                  
                                  //echo preg_replace('/[^0-9]/', '', $partnership);
                                  //echo "\n";
                                }
                                return $partnership;
                                //var_dump($data);
                              });
                              $active = $crawler3->each(function (Crawler $node) use ( $xpaths_crawler3 ) {
                                  foreach($xpaths_crawler3 as $key => $value){
                                      //$data[$key] = array();
                                      //$data[$key] = $node1->filter($value)->text();
                                      $active = trim(preg_replace('/[^0-9]/', '', $node->filter($value)->text()));
                                      //echo preg_replace('/[^0-9]/', '', $active);
                                      //echo "\n";
                                  }
                                  return $active;
                                //echo $node->html();
                              
                              });
                              //$afbsite = $crawler3->each(function (Crawler $node) use ( $active ) {
                                
                                

                                $count_data = $active[0];
                                $afbsite = array();
                                //echo $count_data;

                                for( $i = 1 ; $count_data >= $i ; $i++ ){
                                    $afbsite[$i]['product'] = $product_info->id;

                                    $xpath_for_site = array(
                                      'media_id'=>'#reportTable > tbody > tr:nth-child('.$i.') > td.maxw150',
                                      'site_name'=>'#reportTable > tbody > tr:nth-child('.$i.') > td.maxw150 > p > a',
                                      'imp'=>'#reportTable > tbody > tr:nth-child('.$i.') > td:nth-child(5) > p',
                                      'click'=>'#reportTable > tbody > tr:nth-child('.$i.') > td:nth-child(6) > p',
                                      'cv'=>'#reportTable > tbody > tr:nth-child('.$i.') > td:nth-child(9) > p',
                                      'ctr'=>'#reportTable > tbody > tr:nth-child('.$i.') > td:nth-child(7) > p',
                                      'cvr'=>'#reportTable > tbody > tr:nth-child('.$i.') > td:nth-child(10) > p',
                                      'price'=>'#reportTable > tbody > tr:nth-child('.$i.') > td:nth-child(12) > p',
                                    );

                                  foreach($xpath_for_site as $key => $value){
                                      
                                      if( $key == 'media_id' ){
                                        //$data = trim($node->filter($value)->attr('title'));
                                        $media_id = array();
                                        $sid = trim($crawler3->filter($value)->attr('title'));
                                        preg_match('/SID：(\d+)/', $sid , $media_id);

                                        $afbsite[$i][$key] = $media_id[1];

                                      }elseif( $key == 'site_name' ){
                                      
                                        $afbsite[$i][$key] = trim($crawler3->filter($value)->text());
                                      
                                      }else{
                                      
                                        $afbsite[$i][$key] = trim(preg_replace('/[^0-9]/', '', $crawler3->filter($value)->text()));
                                      
                                      }
                                    $data[$i]['cpa']= $this->cpa($data[$i]['cv'] ,$data[$i]['price'] , 4); 
                                  }
                                }
                                //var_dump($data);

                                //return $data;
                                
                              //});



                              $afbdata[0]['active'] = $active[0];
                              $afbdata[0]['partnership'] = $partnership[0];
                              
                              //var_dump($afbdata);

                              $this->save_daily(json_encode($afbdata));
                              $this->save_site(json_encode($afbsite));

                          }
                    });
    }
    public function run(Request $request){
          $this->a8();
          $this->accesstrade();
          $this->vc();
          $this->afb();
          
          
          return view('cralwerdaily');
        }

  }

