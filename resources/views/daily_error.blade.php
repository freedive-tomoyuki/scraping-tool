@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
              <div class="card">
                  <div class="card-header">検索 </div>
                    <div class="card-body">
                      <form action="{{ url()->current() }}" method="post" class="form-horizontal">
                          @csrf
                          <div class="form-group row">
                            <label class="control-label col-xs-2">プロダクト</label>
                            <div class="col-xs-5">
                                <select name="product" class="form-control">
                                @foreach($product_bases as $product_base)
                                  <option value="{{ $product_base -> id }}">{{ $product_base -> product_name }}</option>
                                @endforeach
                                </select>
                            </div>
                          </div>
                          <div class="form-group row">
                              <label class="control-label col-xs-2">日時</label>
                              <div class="col-xs-5">
                                <input type="date" name="searchdate" class="datepicker form-control" id="datepicker" value="{{old('searchdate')}}">
                              </div>
                          </div>
                          <div class="form-group">
                            <div class="col-xs-offset-2 col-xs-10">
                              <button type="submit" class="btn btn-primary">検索</button>
                            </div>
                          </div>
                      </form>
                    </div>
                  </div>
                </div>
                <div class="col-md-12">
                      <div class="card">
                          <div class="card-header">日次レポート
                            <div class="float-right">
                              <a href='/daily_result_site'>
                                <button type="button" class="btn btn-primary ">サイト別</button>
                              </a>
                              <a href='/daily_result'>
                                <button type="button" class="btn btn-primary ">サマリー</button>
                              </a>
                            </div>
                          </div>
                          <div class="card-body ">
                              <h5 >
                              検索結果がございません。
                              </h5>
                          </div>
                        </div>
                      </div>
                </div>
    </div>
  </div>
</div>

@endsection
