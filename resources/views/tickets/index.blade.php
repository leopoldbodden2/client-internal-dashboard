@extends('layouts.app')

@section('content')
    <div id="main-content" class="container-fluid">
        <div class="form-group row date-range-group">
            <label for="dateRange" class="col-auto col-form-label">Date Range</label>
            <div class="col-auto">
                <select id="dateRange" class="form-control">
                    <option value="7">Last 7 Days</option>
                    <option value="30">Last 30 Days</option>
                    <option value="60">Last 60 Days</option>
                    <option value="90">Last 90 Days</option>
                    @if(auth()->user()->admin)
                        <option value="ALL">All</option>
                    @endif
                </select>
            </div>
        </div>
        <div class="form-group row justify-content-start">
            <div class="col">
                <a href="{{ route('ticket.create') }}" class="btn btn-success btn-sm">New Ticket</a>
            </div>
        </div>
        <div class="row justify-content-start">
            <div class="col">
                <div class="card card-table">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            {!! $html->table(['id'=>'tickets','class' => 'table table-bordered table-striped table-hover border-0']) !!}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.dateRange = localStorage.dateRange?localStorage.dateRange:7;
        document.getElementById('dateRange').value = window.dateRange;
    </script>
    {!! $html->scripts() !!}
    <script defer>
        $(function(){
            $('#dateRange').on('change',function(){
                fetchData();
            });
        });
        function fetchData(){
            window.dateRange = $('#dateRange').val();
            window.LaravelDataTables["tickets"].ajax.reload();
        }
    </script>
@endpush
