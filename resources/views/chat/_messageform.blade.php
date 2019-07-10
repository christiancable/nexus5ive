 <form method="POST" action="">
    @csrf
    <div class="form-group">
        <label class="sr-only" for="text">Message</label>
        <textarea class="form-control" id="text" name="text" rows="3"></textarea>
    </div>
    
    <div class="form-group">
        <input class="btn btn-primary" type="submit" value="Send">
    </div>
</form>