@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            
            @if(isset($result) )
            <h4>　リスティング広告に含むURL一覧 </h4>
            ※キーワードで検索した時にGoogle出稿されているドメインとページ内リンク一覧を表示
            <h6>検索キーワード：{{ $keyword }}</h6>
                    <table class="table">
                        <tr><th>Domain</th><th>タイトル</th><th>サイト内リンク</th></tr>
                        @foreach($result as $u)
                        <tr><td>{{ $u['domain'] }}</td><td>{{ $u['title'] }}</td><td>{{ $u['url'] }}</td></tr>
                        @endforeach
                    </table>
            @else
                キーワードが選択されておりません
            @endif
        </div>
    </div>
</div>
@endsection
