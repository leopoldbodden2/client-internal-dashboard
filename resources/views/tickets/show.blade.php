@extends('layouts.app')

@section('content')
    <div id="main-content" class="container-fluid">
        <div class="row justify-content-start">
            <div class="col">
                <div class="card">
                    <div class="card-header">Ticket {{ $ticket->ticket_id }}</div>
                    <div class="card-body">
                        @include('tickets.ticket_info')

                        <p>
                            Your Message:<br>
                            {{ $ticket->message }}
                        </p>

                        <p>Created {{ $ticket->created_at->diffForHumans() }}</p>
                        <hr>

                        <div class="comment-form">
                            <form action="{{ url('comment') }}" method="POST" class="form">
                                {!! csrf_field() !!}

                                <input type="hidden" name="ticket_id" value="{{ $ticket->id }}">

                                <div class="form-group{{ $errors->has('comment') ? ' has-error' : '' }}">
                                    <textarea rows="10" id="comment" class="form-control" name="comment"></textarea>

                                    @if ($errors->has('comment'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('comment') }}</strong>
                                    </span>
                                    @endif
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
