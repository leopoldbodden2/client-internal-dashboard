@extends('layouts.app')

@section('content')
    <div id="main-content" class="container-fluid">
        <div class="row justify-content-start">
            <div class="col">
                <div class="card">
                    <div class="card-header">Drive Group: {{ $socialCalendar->name }} - Social Media Calendar</div>
                    <div class="card-body p-0">
                        <social-calendar-show :calenderid="'{{ $socialCalendar->id }}'"></social-calendar-show>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
