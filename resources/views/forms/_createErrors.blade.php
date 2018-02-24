@section('javascript')
@parent
@include('javascript._jqueryCreateErrors', [$formContainer])
@endsection
<div class="col-md-12 alert alert-danger" role="alert">
  <ul>
    @foreach($errors as $error)
    <li>{{ $error }}</li>
    @endforeach
  </ul>
</div>