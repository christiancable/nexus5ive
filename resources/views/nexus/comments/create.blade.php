<form action="{{ url('comments') }}" method="POST">
    @csrf
    <input type="hidden" name="user_id" value="{{ $user->id }}">
    <input type="hidden" name="redirect_user" value="{{ $user->username }}">
    <div class="mb-3">
        <input type="text" name="text" class="form-control" autofocus placeholder="Leave a comment">
    </div>

    <div class="mb-3">
        <button type="submit" class="btn btn-primary form-control">Add Comment</button>
    </div>
</form>

@if ($errors->any())
    <p class="alert alert-danger">
        Only a monster would try to leave an empty comment! 
    </p>
@endif