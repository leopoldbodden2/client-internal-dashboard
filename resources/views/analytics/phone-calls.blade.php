@extends('layouts.app')

@section('content')
<div id="main-content" class="container-fluid">
    <div class="form-group row date-range-group">
        <label for="dateRange" class="col-auto col-form-label">Date Range</label>
        <div class="col-auto">
            <select id="dateRange" class="form-control" :dateRange="7">
                <option value="7">Last 7 Days</option>
                <option value="30">Last 30 Days</option>
                <option value="60">Last 60 Days</option>
                <option value="90">Last 90 Days</option>
            </select>
        </div>
    </div>
    <div class="row justify-content-start">
        <div class="col">
            <div class="card card-table">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        {!! $html->table(['id'=>'phone-calls','class' => 'table table-striped table-hover table-sm table-condensed'], true) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('modals')
<div class="modal fade" id="modal-callvalue" tabindex="-1" role="dialog" aria-labelledby="callvalueLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 id="call_title" class="modal-title"></h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="far fa-times"></span>
                </button>
            </div>
            <div class="modal-body justify-content-start">
                <div class="form-group row">
                    <label for="cost_value" class="col-form-label col-4">Value:</label>
                    <div class="col-8">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><span class="far fa-dollar-sign"></span></span>
                            </div>
                            <input type="number" step="0.01" class="form-control" name="cost_value" id="cost_value" aria-label="Amount (to the nearest dollar)">
                        </div>
                    </div>
                    <input type="hidden" name="id" id="call_value_id">
                    <input type="hidden" name="call_id" id="call_id">
                </div>
            </div>
            <div class="modal-footer">
                @csrf
                <button type="reset" class="btn btn-light" data-dismiss="modal">Close</button>
                <button id="saveCostValue" class="btn btn-primary">Save</button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
    <script>
    window.dateRange = localStorage.dateRange?localStorage.dateRange:7;
    document.getElementById('dateRange').value = window.dateRange;
    </script>
    {!! $html->scripts() !!}
    <script defer>
        var table = window.LaravelDataTables["phone-calls"];
        table.on('draw.dt', () => {
            let tabledata = table.ajax.json();
            $( table.column( 4 ).footer() ).html( 'Total Call Value:<br>Average Call Value:' );
            $( table.column( 5 ).footer() ).html( '<strong>'+tabledata.total_sum+'</strong><br>'+tabledata.total_avg );
        });
        $(function(){
            $('#saveCostValue').on('click',function() {
                $('#saveCostValue')[0].disabled = true;
                axios.post(APP_URL+'/api/call-tracking-value/create',{
                        "call_id": $('#modal-callvalue').find('#call_id').val(),
                        "cost_value": $('#modal-callvalue').find('#cost_value').val(),
                        "id": $('#modal-callvalue').find('#call_value_id').val()
                    })
                    .then(response => response.data)
                    .then(data => {
                        if(data.success){

                        }
                        $('#modal-callvalue').find('.modal-body #call_value_id').val('');
                        $('#modal-callvalue').find('.modal-body #call_id').val('');
                        $('#modal-callvalue').find('.modal-body #cost_value').val('');
                        $('#modal-callvalue').find('.modal-header #call_title').html('');
                        $('#modal-callvalue').modal('hide');
                        $('#saveCostValue')[0].disabled = false;
                        fetchData();
                    })
                    .catch(error => {
                        console.log(error);
                    });
            });
            $('#modal-callvalue').on('show.bs.modal', function(event) {
                var shownButton = $(event.relatedTarget);
                var modal = $(this);
                modal.find('.modal-body #call_value_id').val(shownButton.data('callvalueid'));
                modal.find('.modal-body #call_id').val(shownButton.data('callid'));
                modal.find('.modal-body #cost_value').val(shownButton.data('costvalue'));
                modal.find('.modal-header #call_title').html(shownButton.data('calltitle'));
            }).on('hide.bs.modal', function(event) {
                var modal = $(this);
                modal.find('.modal-body #call_value_id').val('');
                modal.find('.modal-body #call_id').val('');
                modal.find('.modal-body #cost_value').val('');
                modal.find('.modal-header #call_title').html('');
                $('#saveCostValue')[0].disabled = false;
            });
            $('#dateRange').on('change',function(){
                fetchData();
            });
        });
        function fetchData(){
            window.dateRange = $('#dateRange').val();
            window.LaravelDataTables["phone-calls"].ajax.reload();
        }
    </script>
@endpush
