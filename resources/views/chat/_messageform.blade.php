 <form method="POST" action="">
    @csrf
  <div class="form-row">
    <div class="col-9 col-md-10">
      <label class="sr-only" for="text">Message</label>
      <textarea class="form-control" id="text" name="text" rows="3"></textarea>
    </div>
    <div class="col">
       <input class="btn btn-primary" type="submit" value="Send">
    </div>
  </div>
</form>