@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="form-group row date-range-group">
        <label for="date_range" class="col-auto col-form-label">Date Range</label>
        <div class="col-auto">
            <select class="form-control" id="date_range" v-model="date_range" @change="onChange">
                <option value="7">Last 7 Days</option>
                <option value="30">Last 30 Days</option>
                <option value="60">Last 60 Days</option>
                <option value="90">Last 90 Days</option>
            </select>
        </div>
    </div>
    <div class="row justify-content-start">
        <div class="col-10">
            {!! $html->table(['id'=>'reviews','class' => 'table table-striped table-hover table-bordered bg-white'], true) !!}
        </div>
    </div>
</div>
@endsection

@push('modals')
<div class="modal fade" id="modal-callvalue" tabindex="-1" role="dialog" aria-labelledby="callvalueLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <p id="call_title" class="modal-title"></p>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="far fa-times"></span>
                </button>
            </div>
            <form action="{{ route('call-tracking-value.create') }}">
                <div class="modal-body">
                    <div class="form-group">
                        <label for="cost_value" class="col-form-label">Value:</label>
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text"><span class="far fa-dollar-sign"></span></span>
                            </div>
                            <input type="number" step="0.01" class="form-control" name="cost_value" id="cost_value" aria-label="Amount (to the nearest dollar)">
                        </div>
                        <input type="hidden" name="id" id="call_value_id">
                        <input type="hidden" name="call_id" id="call_id">
                    </div>
                </div>
                <div class="modal-footer">
                    @csrf
                    <button type="reset" class="btn btn-light" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endpush

@push('scripts')
    {!! $html->scripts() !!}
    <script defer>
        var table = window.LaravelDataTables["reviews"];
        table.on('draw.dt', () => {
            let tabledata = table.ajax.json();
            $( table.column( 4 ).footer() ).html( 'Total Call Value:<br>Avg Call Value:' );
            $( table.column( 5 ).footer() ).html( '<strong>'+tabledata.total_sum+'</strong><br>'+tabledata.total_avg );
        });
        new Vue({
            el: '#date_range',
            data: {
              date_range: 7
            },
            methods: {
                onChange(event){
                    window.dateRange = this.date_range;
                    window.LaravelDataTables["reviews"].ajax.reload();
                }
            },
            mounted(){
                window.dateRange = this.date_range;
            }
        })
        $('#modal-callvalue').on('show.bs.modal', function(event) {
            var button = $(event.relatedTarget);
            var call_id = button.data('callid');
            var cost_value = button.data('costvalue');
            var call_title = button.data('calltitle');
            var call_value_id = button.data('callvalueid');

            var modal = $(this);
            modal.find('.modal-body #call_value_id').val(call_value_id);
            modal.find('.modal-body #call_id').val(call_id);
            modal.find('.modal-body #cost_value').val(cost_value);
            modal.find('.modal-header #call_title').html(call_title);
        });
    </script>
@endpush
