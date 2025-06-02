@if ($alert = Session::get('alert'))
    <div class="alert alert-{{ $alert['messageType'] }} alert-dismissible fade show">
        <button type="button" class="close" data-dismiss="alert">&times;</button>
        <strong>
            {{ $alert['message'] }}
        </strong>
    </div>
@endif