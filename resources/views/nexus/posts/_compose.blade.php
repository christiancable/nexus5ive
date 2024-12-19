<form action="{{ url('posts') }}" method="POST">
    @csrf
    <input type="hidden" name="topic_id" value="{{ $topic->id }}">

    <div class="mb-3">
        <input type="text" name="title" class="form-control" placeholder="Subject">
    </div>

    <div class="mb-3">
        <textarea name="text" class="form-control" id="postText"></textarea>
    </div>
</form>
