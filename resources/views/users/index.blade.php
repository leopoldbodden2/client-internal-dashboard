@extends('layouts.app')

@section('content')
<div id="main-content" class="container-fluid">
    <div class="form-group row justify-content-start">
        <div class="col">
            <button data-toggle="modal" data-target="#modal-user-create" class="btn btn-success btn-sm">Create User</button>
        </div>
    </div>
    <div class="row justify-content-start">
        <div class="col">
            <div class="card card-table">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        {!! $html->table(['id'=>'users','class' => 'table table-striped table-hover'], true) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<div class="modal fade" id="modal-user-create" tabindex="-1" role="dialog" aria-labelledby="useredit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Create User</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="far fa-times"></span>
                </button>
            </div>
            <form id="createForm" role="form" onsubmit="return createUserData(event)">
                <div class="modal-body justify-content-start">
                    <div class="row error-holder" style="display:none;">
                        <div class="col">
                            <div id="create_errors" class="alert alert-danger">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-form-label col-4">Name:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="name" id="create_name" aria-label="Name" required  autocomplete="full-name">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-form-label col-4">Email:</label>
                        <div class="col-8">
                            <input type="email" class="form-control" name="email" id="create_email" aria-label="Email" required autocomplete="email">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="client_id" class="col-form-label col-4">Client:</label>
                        <div class="col-8">
                            <select class="form-control" name="client_id" id="create_client_id" aria-label="Client" required>
                                <option value=""></option>
                                @foreach(App\Client::orderBy('prefix','asc')->get() as $client)
                                    <option value="{{ $client->id }}">{{ $client->prefix }} - {{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="analytics" class="col-form-label col-4">Analytics:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="analytics" id="create_analytics" aria-label="Analytics">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ctm_id" class="col-form-label col-4">CTM Account ID:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="ctm_id" id="create_ctm_id" aria-label="CTM Id">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ctm_auth" class="col-form-label col-4">CTM Access Key:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="ctm_auth[0]" id="create_ctm_auth1" aria-label="CTM Auth 1">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ctm_auth" class="col-form-label col-4">CTM Secret Key:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="ctm_auth[1]" id="create_ctm_auth2" aria-label="CTM Auth 2">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="create_cdyne_license_key" class="col-form-label col-4">Cdyne License Key:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="create_cdyne_license_key" id="create_cdyne_license_key" aria-label="Cdyne License Key">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="create_cdyne_phone" class="col-form-label col-4">Cdyne Phone:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="create_cdyne_phone" id="create_cdyne_phone" aria-label="Cdyne Phone">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-form-label col-4">Password</label>
                        <div class="col-8">
                            <input id="create_password" type="password" class="form-control" name="password" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="create_password_confirmation" class="col-form-label col-4">Confirm Password</label>
                        <div class="col-8">
                            <input id="create_password_confirmation" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <div class="checkbox">
                                <label for="create_admin" class="col-form-label">
                                    <input id="create_admin" type="checkbox" name="admin" value="1"> Admin?
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-dismiss="modal">Close</button>
                    <button id="createData" class="btn btn-primary" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-user-edit" tabindex="-1" role="dialog" aria-labelledby="useredit" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title">Edit User</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span class="far fa-times"></span>
                </button>
            </div>
            <form id="editForm" role="form" onsubmit="return saveUserData(event);">
                <div class="modal-body justify-content-start">
                    <div class="row error-holder" style="display:none;">
                        <div class="col">
                            <div id="errors" class="alert alert-danger">
                            </div>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="name" class="col-form-label col-4">Name:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="name" id="name" aria-label="Name" autocomplete="full-name" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="email" class="col-form-label col-4">Email:</label>
                        <div class="col-8">
                            <input type="email" class="form-control" name="email" id="email" aria-label="Email" autocomplete="email" required>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="client_id" class="col-form-label col-4">Client:</label>
                        <div class="col-8">
                            <select class="form-control" name="client_id" id="client_id" aria-label="Client" required>
                                <option value=""></option>
                                @foreach(App\Client::orderBy('prefix','asc')->get() as $client)
                                    <option value="{{ $client->id }}">{{ $client->prefix }} - {{ $client->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="analytics" class="col-form-label col-4">Analytics:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="analytics" id="analytics" aria-label="Analytics">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ctm_id" class="col-form-label col-4">CTM Account ID:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="ctm_id" id="ctm_id" aria-label="CTM Id">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ctm_auth" class="col-form-label col-4">CTM Access Key:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="ctm_auth[0]" id="ctm_auth1" aria-label="CTM Auth 1">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="ctm_auth" class="col-form-label col-4">CTM Secret Key:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="ctm_auth[1]" id="ctm_auth2" aria-label="CTM Auth 2">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cdyne_license_key" class="col-form-label col-4">Cdyne License Key:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="cdyne_license_key" id="cdyne_license_key" aria-label="Cdyne License Key">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="cdyne_phone" class="col-form-label col-4">Cdyne Phone:</label>
                        <div class="col-8">
                            <input type="text" class="form-control" name="cdyne_phone" id="cdyne_phone" aria-label="Cdyne Phone">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password" class="col-form-label col-4">Password</label>
                        <div class="col-8">
                            <input id="password" type="password" class="form-control" autocomplete="new-password" name="password">
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="password_confirmation" class="col-form-label col-4">Confirm Password</label>
                        <div class="col-8">
                            <input id="password_confirmation" type="password" class="form-control" name="password_confirmation" autocomplete="new-password">
                        </div>
                    </div>
                    <div class="form-group row">
                        <div class="col">
                            <div class="checkbox">
                                <label for="admin" class="col-form-label">
                                    <input id="admin" type="checkbox" name="admin" value="1"> Admin?
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-light" data-dismiss="modal">Close</button>
                    <button id="saveData" class="btn btn-primary" type="submit">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>



    {!! $html->scripts() !!}
    <script>
        var table = window.LaravelDataTables["users"];
        var $userEditModal;
        var $userCreateModal;
        var editUserId;
        $(function(){
            $userEditModal = $('#modal-user-edit');
            $userCreateModal = $('#modal-user-create');

            $userEditModal.on('hide.bs.modal', function(event) {
                $userEditModal.find('.modal-body #client_id :selected').removeAttr('selected');
                $userEditModal.find('.modal-body #name').val('');
                $userEditModal.find('.modal-body #email').val('');
                $userEditModal.find('.modal-body #analytics').val('');
                $userEditModal.find('.modal-body #ctm_id').val('');
                $userEditModal.find('.modal-body #ctm_auth1').val('');
                $userEditModal.find('.modal-body #ctm_auth2').val('');
                $userEditModal.find('.modal-body #cdyne_phone').val('');
                $userEditModal.find('.modal-body #cdyne_license_key').val('');
                $userEditModal.find('.modal-body #password').val('');
                $userEditModal.find('.modal-body #password_confirmation').val('');
                $userEditModal.find('.modal-body #admin').removeAttr('checked');
                document.querySelector('#saveData').disabled = false;
                $userEditModal.find('.modal-body #errors').html('');
                $userEditModal.find('.modal-body .error-holder').hide();
            });

            $userCreateModal.on('hide.bs.modal', function(event) {
                $userCreateModal.find('.modal-body #create_client_id :selected').removeAttr('selected');
                $userCreateModal.find('.modal-body #create_name').val('');
                $userCreateModal.find('.modal-body #create_email').val('');
                $userCreateModal.find('.modal-body #create_analytics').val('');
                $userCreateModal.find('.modal-body #create_ctm_id').val('');
                $userCreateModal.find('.modal-body #create_ctm_auth1').val('');
                $userCreateModal.find('.modal-body #create_ctm_auth2').val('');
                $userCreateModal.find('.modal-body #create_cdyne_phone').val('');
                $userCreateModal.find('.modal-body #create_cdyne_license_key').val('');
                $userCreateModal.find('.modal-body #create_password').val('');
                $userCreateModal.find('.modal-body #create_password_confirmation').val('');
                $userCreateModal.find('.modal-body #create_admin').removeAttr('checked');
                document.querySelector('#createData').disabled = false;
                $userCreateModal.find('.modal-body #create_errors').html('');
                $userCreateModal.find('.modal-body .error-holder').hide();
            });
        });
        function createUserData(e){
            e.preventDefault();
            document.querySelector('#createData').disabled = true;
            let ctm_auth = [
                $userCreateModal.find('.modal-body #create_ctm_auth1').val(),
                $userCreateModal.find('.modal-body #create_ctm_auth2').val()
            ];
            let userData = {
               'name': $userCreateModal.find('.modal-body #create_name').val(),
               'email': $userCreateModal.find('.modal-body #create_email').val(),
               'analytics': $userCreateModal.find('.modal-body #create_analytics').val(),
               'ctm_id': $userCreateModal.find('.modal-body #create_ctm_id').val(),
               'ctm_auth': ctm_auth.join(','),
                'cdyne_phone': $userCreateModal.find('.modal-body #create_cdyne_phone').val(),
                'cdyne_license_key': $userCreateModal.find('.modal-body #create_cdyne_license_key').val(),
               'client_id': $userCreateModal.find(`.modal-body #create_client_id :selected`).val(),
               'password': $userCreateModal.find('.modal-body #create_password').val(),
               'password_confirmation': $userCreateModal.find('.modal-body #create_password_confirmation').val(),
               'admin': $userCreateModal.find('.modal-body #create_admin').is(':checked')
            };

            axios.post(`${APP_URL}/api/users`,userData)
                 .then(response => response.data)
                 .then(data => {
                    if(data.success){
                        $userCreateModal.modal('hide');
                        fetchData();
                    }
                    else{
                        document.querySelector('#createData').disabled = false;
                    }
                 })
                 .catch(error => {
                     document.querySelector('#createData').disabled = false;
                     $userCreateModal.find('.modal-body .error-holder').show();
                     if (typeof error.response.data === 'object') {
                         $userCreateModal.find('.modal-body #create_errors').html(_.flatten(_.toArray(error.response.data.errors)));
                     } else {
                        $userCreateModal.find('.modal-body #create_errors').html('Something went wrong. Please try again.');
                     }
                 });
            return false;
        }
        function saveUserData(e){
            e.preventDefault();
            document.querySelector('#saveData').disabled = true;
            let ctm_auth = [
                $userEditModal.find('.modal-body #ctm_auth1').val(),
                $userEditModal.find('.modal-body #ctm_auth2').val()
            ];
            let userData = {
               'name': $userEditModal.find('.modal-body #name').val(),
               'email': $userEditModal.find('.modal-body #email').val(),
               'analytics': $userEditModal.find('.modal-body #analytics').val(),
               'ctm_id': $userEditModal.find('.modal-body #ctm_id').val(),
               'ctm_auth': ctm_auth.join(','),
                'cdyne_phone': $userEditModal.find('.modal-body #cdyne_phone').val(),
                'cdyne_license_key': $userEditModal.find('.modal-body #cdyne_license_key').val(),
               'client_id': $userEditModal.find(`.modal-body #client_id :selected`).val(),
               'password': $userEditModal.find('.modal-body #password').val(),
               'password_confirmation': $userEditModal.find('.modal-body #password_confirmation').val(),
               'admin': $userEditModal.find('.modal-body #admin').is(':checked')
            };

            axios.put(`${APP_URL}/api/users/${editUserId}`,userData)
                 .then(response => response.data)
                 .then(data => {
                    if(data.success){
                        $userEditModal.modal('hide');
                        fetchData();
                    }
                    else{
                        document.querySelector('#saveData').disabled = false;
                    }
                 })
                 .catch(error => {
                     $userEditModal.find('.modal-body .error-holder').show();
                     document.querySelector('#saveData').disabled = false;
                     if (typeof error.response.data === 'object') {
                        $userEditModal.find('.modal-body #errors').html(_.flatten(_.toArray(error.response.data.errors)));
                     } else {
                        $userEditModal.find('.modal-body #errors').html('Something went wrong. Please try again.');
                     }
                 });
            return false;
        }
        function editUser(userid){
            editUserId = userid;
            axios.get(`${APP_URL}/api/users/${editUserId}`)
                 .then(response => response.data)
                 .then(data => {
                    $userEditModal.find('.modal-body #name').val(data.name);
                    $userEditModal.find('.modal-body #email').val(data.email);
                    $userEditModal.find('.modal-body #analytics').val(data.analytics);
                    $userEditModal.find('.modal-body #ctm_id').val(data.ctm_id);
                    $userEditModal.find('.modal-body #ctm_auth1').val(data.ctm_auth[0]);
                    $userEditModal.find('.modal-body #ctm_auth2').val(data.ctm_auth[1]);
                     $userEditModal.find('.modal-body #cdyne_phone').val(data.cdyne_phone);
                     $userEditModal.find('.modal-body #cdyne_license_key').val(data.cdyne_license_key);
                    $userEditModal.find(`.modal-body #client_id option[value="${data.client_id}"]`).attr('selected','selected');
                    $userEditModal.find(`.modal-body #admin`).prop('checked',data.admin);
                    $userEditModal.modal('show');
                 })
                 .catch(error => {
                     $userEditModal.find('.modal-body .error-holder').show();
                     document.querySelector('#saveData').disabled = false;
                     if (typeof error.response.data === 'object') {
                        $userEditModal.find('.modal-body #errors').html(_.flatten(_.toArray(error.response.data.errors)));
                     } else {
                        $userEditModal.find('.modal-body #errors').html('Something went wrong. Please try again.');
                     }
                 });
        }
        function fetchData(){
            window.LaravelDataTables["users"].ajax.reload();
        }
    </script>
@endpush
