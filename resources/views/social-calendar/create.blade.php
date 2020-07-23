@extends('layouts.app')

@section('content')
    <div id="main-content" class="container-fluid">
        <div class="row justify-content-start">
            <div class="col">
                <div class="card">
                    <div class="card-header">Create New Social Media Calendar</div>
                    <div class="card-body">
                        <form class="form-horizontal" role="form" method="POST" action="{{ route('social-calendar.store') }}">
                            {!! csrf_field() !!}

                            <div class="form-group{{ $errors->has('name') ? ' has-error' : '' }}">
                                <label for="name" class="col-md-4 control-label">Name</label>

                                <div class="col-md-6">
                                    <input id="name" type="text" class="form-control" name="name" value="{{ old('name') }}">

                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('name') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <div class="form-group{{ $errors->has('user_id') ? ' has-error' : '' }}">
                                <label for="user_id" class="col-form-label col-md-4">User:</label>
                                <div class="col-md-6">
                                    <select class="form-control" name="user_id" id="create_user_id" aria-label="Client" required>
                                        <option value=""></option>
                                        @foreach(App\User::orderBy('name','asc')->get() as $user)
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endforeach
                                    </select>


                                    @if ($errors->has('name'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('user_id') }}</strong>
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <social-calendar-create></social-calendar-create>

                            <div class="form-group">
                                <div class="col-md-6 col-md-offset-4">
                                    <button type="submit" class="btn btn-outline-black">
                                        Save Calendar
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection



