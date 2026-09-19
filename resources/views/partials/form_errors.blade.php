@if ($errors->any())
    <div class="err-banner">
        <b><i class="fa-solid fa-circle-exclamation"></i> Revisa los siguientes campos:</b>
        <ul>@foreach ($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
@endif
