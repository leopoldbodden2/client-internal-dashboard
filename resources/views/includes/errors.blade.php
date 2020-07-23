@if ($errors->any())
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col">
                <div role="alert" class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endif
