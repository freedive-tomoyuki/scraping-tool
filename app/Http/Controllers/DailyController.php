<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Dailydata;
use App\Product;
use App\Dailysite;
use App\ProductBase;
use Symfony\Component\HttpFoundation\StreamedResponse;


class DailyController extends Controller
{
    public function daily_result() {
        //Dailydata::
        //echo $id = ($id != null)? $id : 1 ;
        //echo $searchdate = ($searchdate != null)? $searchdate : date("Y-m-d");
        
        $products = Dailydata::select(['name', 'imp', 'click','cv', 'cvr', 'ctr', 'active', 'partnership','dailydatas.created_at','products.product','products.id'])
                    ->join('products','dailydatas.product_id','=','products.id')
                    ->join('asps','products.asp_id','=','asps.id')
                    ->where('product_base_id', 1)
                    ->where('dailydatas.created_at', 'LIKE' , "%".date("Y-m-d")."%")
                    ->get();
                    //->toSql();

        $product_bases = ProductBase::all();
        //Where('product_base_id', 1);
        //$product = $product->dailydata;

        //echo $product ->toSql();

        //echo "<pre>";
        //var_dump($products);
        //echo "</pre>";
/*        $product
        ->where( "", $id )
        ->where( "", $type )
*/
        //var_dump($products);
        if( $products->isEmpty() ){
        	return view('daily_error',compact('product_bases'));
        }else{
        	return view('daily',compact('products','product_bases'));
        }
    }
	public function daily_result_search(Request $request) {
 
		$id = ($request->product != null)? $request->product : 1 ;
        $searchdate =($request->searchdate != null)? $request->searchdate : date("Y-m-d");

		//echo $post->product;
		//echo $post->searchdate;

        $products = Dailydata::select(['name', 'imp', 'click','cv', 'cvr', 'ctr', 'active', 'partnership','dailydatas.created_at','products.product','products.id'])
                    ->join('products','dailydatas.product_id','=','products.id')
                    ->join('asps','products.asp_id','=','asps.id')
                    ->where('product_base_id', $id)
                    ->where('dailydatas.created_at', 'LIKE' , "%".$searchdate."%")
                    ->get();
                    //->toSql();

        $product_bases = ProductBase::all();

/* 		foreach($products as $key => $value){
 			if($key == 'created_at'){
 				$date = $value;
 			}
		}
*/
        if( $products->isEmpty() ){
        	return view('daily_error',compact('product_bases'));
        }else{
        	return view('daily',compact('products','product_bases'));
        }


    }
    public function daily_result_site() {
        //Dailydata::
        //echo $id = ($id != null)? $id : 1 ;
        //echo $searchdate = ($searchdate != null)? $searchdate : date("Y-m-d");
        //$products = Dailysite::find(1)->get();

        $products = Dailysite::select(['name', 'imp', 'click','cv', 'cvr', 'ctr', 'media_id','site_name','dailysites.created_at','products.product','products.id'])
                    ->join('products','dailysites.product_id','=','products.id')
                    ->join('asps','products.asp_id','=','asps.id')
                    ->where('product_base_id', 1)
                    ->where('dailysites.created_at', 'LIKE' , "%".date("Y-m-d")."%")
                    ->get();
                    //->toSql();
                    //echo $products;
        $product_bases = ProductBase::all();

        
        if( $products->isEmpty() ){
        	return view('daily_error',compact('product_bases'));
        }else{
        	return view('daily_site',compact('products','product_bases'));
        }
    }
	public function daily_result_site_search(Request $post) {

		$post->session()->get('searchdate');

		$id = ($post->product != null)? $post->product : 1 ;
        $searchdate =($post->searchdate != null)? $post->searchdate : date("Y-m-d");

		//echo $post->product;
		//echo $post->searchdate;

        $products = Dailysite::select(['name', 'imp', 'click','cv', 'cvr', 'ctr', 'media_id','site_name','dailysites.created_at','products.product','products.id'])
                    ->join('products','dailysites.product_id','=','products.id')
                    ->join('asps','products.asp_id','=','asps.id')
                    ->where('product_base_id', $id)
                    ->where('dailysites.created_at', 'LIKE' , "%".$searchdate."%")
                    ->get();
                    //->toSql();

        $product_bases = ProductBase::all();
        //var_dump($products);
        
        //echo $products->isEmpty();

        //echo $product_bases->isEmpty();

        if( $products->isEmpty() ){
        	return view('daily_error',compact('product_bases'));
        }else{
        	return view('daily_site',compact('products','product_bases'));
        }


    }
    public function downloadCSV( $id , $date )
    {
    	$date = urldecode($date);
        return  new StreamedResponse(
            function () use ($date,$id){
            	//$csv = array();
                
                $stream = fopen('php://output', 'w');

                fputcsv($stream, ['ASP',  'imp', 'click','cv', 'cvr', 'ctr', 'active', 'partnership']);

                //array_unshift($data, $csvHeaders);
                $csv= Dailydata::
                	select(['name', 'imp', 'click','cv', 'cvr', 'ctr', 'active', 'partnership'])
                    ->join('products','dailydatas.product_id','=','products.id')
                    ->join('asps','products.asp_id','=','asps.id')
                    ->where('product_base_id', $id)
                    ->where('dailydatas.created_at', 'LIKE' , "%".$date."%")
                    ->get()
                	->toArray();
                	//var_dump($csv);

                foreach ($csv as $line) {
                    fputcsv($stream, $line);
                }
                fclose($stream);

            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$date.'_dailydata.csv"',
            ]
        );
    }
    public function downloadSiteCSV( $id , $date )
    {
    	$date = urldecode($date);
        return  new StreamedResponse(
            function () use ($date,$id){
            	//$csv = array();
                
                $stream = fopen('php://output', 'w');

                fputcsv($stream, ['ASP', 'MediaID','サイト名', 'imp', 'click','cv', 'CVR', 'CTR']);

                //array_unshift($data, $csvHeaders);
                $csv= Dailysite::
                	select(['name', 'media_id','site_name', 'imp', 'click','cv', 'cvr', 'ctr'])
                    ->join('products','dailysites.product_id','=','products.id')
                    ->join('asps','products.asp_id','=','asps.id')
                    ->where('product_base_id', $id)
                    ->where('dailysites.created_at', 'LIKE' , "%".$date."%")
                    ->get()
                	->toArray();
                	//var_dump($csv);

                foreach ($csv as $line) {
                    fputcsv($stream, $line);
                }
                fclose($stream);

            },
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => 'attachment; filename="'.$date.'_dailysites.csv"',
            ]
        );
    }
}
