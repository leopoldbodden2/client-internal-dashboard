@if (session('alert'))
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col">
                <div class="alert alert-{{ session('alert-type') }}" role="alert">
                    {{ session('alert') }}
                </div>
            </div>
        </div>
    </div>
@endif
