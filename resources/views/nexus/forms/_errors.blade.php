<div class="alert alert-danger" role="alert">
  @if ( count($errors) == 1)
    {{ $errors[0] }}
  @else 
    <ul class="mb-0">
      @foreach($errors as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  @endif 
</div>