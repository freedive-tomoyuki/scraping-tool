<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/
/*Route::get('daily_report', function () {
    return view('cralwerdaily');
})->name('crawlerdaily');*/

//特例リンク
Route::get('imoto','ImotoController@index');



Route::get('show_test','ChromeheadlessController@show_test')->name('show_test');

Route::get('daily_report','ChromeheadlessController@index')->name('crawlerdaily');

Route::get( 'test','CrawlerController@index');
// Route::get( 'daily_result','DailyController@daily_result')->name('daily_result');
// Route::post( 'daily_result','DailyController@daily_result_search');

// Route::get( 'daily_result_site','DailyController@daily_result_site')->name('daily_result_site');
// Route::post( 'daily_result_site','DailyController@daily_result_site_search');

// //Route::post('/daily_report','ChromeheadlessController@run');

// Route::get('/', 'DailydataController@index');

// Route::get('/afb', 'DailydataController@afb');

// Route::get('/at', 'DailydataController@at');

// Route::get('/vc', 'DailydataController@vc');

// Route::get('/test','ScrapingController@index');

// Route::post('daily_report','ChromeheadlessController@run');

// Route::get('csv/{id}/{date}','DailyController@downloadCSV');
// Route::get('csv_site/{id}/{date}','DailyController@downloadSiteCSV');

Route::get('/exec', 'ScrapingController@index');
Route::post('/exec/run', 'ScrapingController@run')->name('running');


/*成功例
Route::get('/demo', function() {
   $crawler = Goutte::request('GET', 'https://www.rentracks.co.jp/ir/f_results/');
   $crawler->filter('table tr')->each(function ($node) {
    $th = $node->filter('th')->text();
    $td = $node->filter('td')->text();
    var_dump($td);
   });
   return view('welcome');
});*/
Auth::routes();

//Route::get('/', 'HomeController@index')->name('home');
