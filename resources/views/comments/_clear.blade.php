<form action="{{ action('Nexus\CommentController@destroyAll') }}" method="POST">
    @csrf
    @method('DELETE')
    <input type="hidden" name="user_id" value="{{ $user->id }}">
    <input type="hidden" name="redirect_user" value="{{ $user->username }}">
    
    <div class="form-group">
        <button type="submit" class="btn btn-danger form-control">Clear All Comments</button>
    </div>
</form>
