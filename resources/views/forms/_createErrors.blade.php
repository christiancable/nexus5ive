@if (Session::get('form') == $formName)
  @if ($errors->all())
    @section('javascript')
    @parent
    @include('javascript._jqueryCreateErrors', [$formContainer])
    @endsection
    <div class="col-md-12 alert alert-danger" role="alert">
      <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
@endif