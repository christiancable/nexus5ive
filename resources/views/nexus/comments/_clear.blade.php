<form action="{{ action('App\Http\Controllers\Nexus\CommentController@destroyAll') }}" method="POST">
    @csrf
    @method('DELETE')
    <input type="hidden" name="user_id" value="{{ $user->id }}">
    <input type="hidden" name="redirect_user" value="{{ $user->username }}">
    
    <div class="mb-3">
        <button type="submit" class="btn btn-danger form-control" dusk="btn-clear-all-comments">Clear All Comments</button>
    </div>
</form>
