@if ($errors->any())
<div class="alert alert-danger alert-dismissible fade show" role="alert">
    <button type="button" class="btn-close" data-coreui-dismiss="alert" aria-label="Close"></button>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
