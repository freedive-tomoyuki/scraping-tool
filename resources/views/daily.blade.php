@extends('layouts.app')

@section('content')

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
                <div class="card">
                  <div class="card-header">検索 </div>
                    <div class="card-body">
                      <form action="/daily_result" method="post" class="form-horizontal">
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
                                <input type="date" name="searchdate" class="datepicker form-control" id="datepicker" value="{{ old('searchdate') }}">
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
              <div class="card">
                <div class="card-header">日次レポート( Imp / Click / CV / アクティブ数　/ 提携数) 
                    <div class="float-right">
                      <a href='/daily_result_site'><button type="button" class="btn btn-primary ">サイト別</button></a>

                      

                      <a href='/csv/{{ $products[0]->id }}/{{ $products[0]->created_at->format("Y-m-d") }}'><button type="button" class="btn btn-primary ">ＣＳＶ</button></a>
                    </div>
                </div>
                <div class="card-body">
                  <div class="col-sm-12">
                    <div>
                        日付：
                         {{ $products[0]->created_at->format("Y/m/d")}}
                    </div>
                    <div>
                        案件：
                          {{ $products[0]->product }}
                    
                    </div>
                  </div>
                <table class="table table-striped table-hover">
                  <thead>
                        <tr>
                            <th>No</th>
                            <th>ASP</th>
                            <th>Imp</th>
                            <th>Click</th>
                            <th>CV</th>
                            <th>CVR</th>
                            <th>CTR</th>
                            <th>アクティブ数</th>
                            <th>提携数</th>
                        </tr>
                  </thead>
                <tbody>
                    <?php $i = 1; ?>
                    
                    @foreach($products as $product)
                    <tr>
                        <td><?php echo $i; ?></td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->imp }}</td>
                        <td>{{ $product->click }}</td>
                        <td>{{ $product->cv }}</td>
                        <td>{{ $product->cvr }}</td>
                        <td>{{ $product->ctr }}</td>
                        <td>{{ $product->active }}</td>
                        <td>{{ $product->partnership }}</td>
                        <?php $i++; ?>
                    </tr>
                    @endforeach

                    
                </tbody>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
