@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">サイト検証</div>

                <div class="card-body">
                    
                    <form method='post' action="{{ route('running')}}" >
                        @csrf
                        <div class="form-group">
                            <label for="keyword">検索キーワード:</label>
                            <input class="form-control " name="keyword" type="text" id='keyword' value="{{ old('keyword') }}" placeholder="複数キーワード検索する場合、カンマ（ , ）で区切って入力してください" >
                        </div>
                        <div class="form-group">
                            <label for="urls">対象サイトURL:</label>
                            <textarea class="form-control " name="urls" value='urls' id='urls' >{{ old('urls') }}</textarea>
                            <div>※複数URLクロールする場合、改行して入力してください。</div>
                        </div>
                        <div class="form-group">
                            <input class="btn btn-primary " type="submit" value='実行' >
                        </div>
                        
                    </form>

                </div>
            </div>
            @if(isset($result) )
            <h4>キーワードを含むサイト</h4>
                    <table class="table">
                        <tr><th>URL</th><th>キーワード</th></tr>
                        @foreach($result as $s)
                        <tr><td>{{ $s[0] }}</td><td>{{ $s[1] }}</td></tr>
                        @endforeach
                    </table>
                    @endif
        </div>
    </div>
</div>
@endsection
