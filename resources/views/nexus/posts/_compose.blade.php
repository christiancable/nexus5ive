<form action="{{ url('posts') }}" method="POST">
    @csrf
    <input type="hidden" name="topic_id" value="{{ $topic->id }}">

    <div class="form-group">
        <input type="text" name="title" class="form-control" placeholder="Subject">
    </div>

    <div class="form-group">
        <textarea name="text" class="form-control" id="postText"></textarea>
    </div>
</form>
