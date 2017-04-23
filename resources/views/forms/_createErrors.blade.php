@if (Session::get('form') == $formName)
  @if ($errors->all())

    <script tyle="text/javascript">
      var el = document.getElementById('{{$formContainer}}');
      if (el.classList) {
        el.classList.add('in');
      } else {
        el.className += ' ' + 'in';
      }
      var distance = el.offsetTop;
      window.scrollTo(0, distance);
    </script>
    <div class="col-md-12 alert alert-danger" role="alert">
      <ul>
        @foreach($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
      </ul>
    </div>
  @endif
@endif