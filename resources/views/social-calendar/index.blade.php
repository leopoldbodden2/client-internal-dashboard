@extends('layouts.app')

@section('content')
    <div id="main-content" class="container-fluid">
        <div class="col">
            <div class="card">
                <div class="card-body">
                    @if(auth()->user()->admin)
                        <div class="form-group">
                            <a href="{{ route('social-calendar.create') }}" class="btn btn-success btn-sm">
                                New Social Media Calendar
                            </a>
                        </div>
                    @endif
                    <div class="table-responsive">
                        {!! $html->table(['id'=>'social-calendar','class' => 'table table-bordered table-striped table-hover border-0']) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        window.dateRange = localStorage.dateRange ? localStorage.dateRange : 7;
        document.getElementById('dateRange').value = window.dateRange;
    </script>
    {!! $html->scripts() !!}
    <script defer>
        $(function() {
            $('#dateRange').on('change', function() {
                fetchData();
            });
        });

        function fetchData() {
            window.dateRange = $('#dateRange').val();
            window.LaravelDataTables['social-calendar'].ajax.reload();
        }
    </script>
@endpush
