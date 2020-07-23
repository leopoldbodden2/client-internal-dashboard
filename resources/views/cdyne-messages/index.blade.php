@extends('layouts.app')

@section('content')
<div id="main-content" class="container-fluid">
    <div class="form-group row justify-content-start">
        <div class="col">
            <cdyne-messages></cdyne-messages>
        </div>
    </div>
    <div class="row justify-content-start">
        <div class="col">
            <div class="card card-table">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        {!! $html->table() !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
    {!! $html->scripts() !!}
@endpush
