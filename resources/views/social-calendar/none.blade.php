@extends('layouts.app')

@section('content')
    <div id="main-content" class="container-fluid">
        <div class="row justify-content-start">
            <div class="col">
                <div class="card">
                    <div class="card-header">Drive Group: Social Media Calendar</div>
                    <div class="card-body">
                        <!--
                        {{auth()->user()->name}}
                        -->
                        <p>There are no calendars available for you to review</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
