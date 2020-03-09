@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">All Connect Daily Report</div>

                <div class="card-body">
                    
                    <form method='post' action="{{ route('crawlerdaily')}}">
                        @csrf
                        <input class="btn btn-primary" type="submit" value='run' >
                    </form>
                    
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
