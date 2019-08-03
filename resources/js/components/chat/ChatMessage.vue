<template>
  <div class="card bg-transparent border-0">
    <div class="card-body pl-1 pt-0 pb-1">
      <p class="card-title d-flex justify-content-between">
        <strong
          :class="[
            message.author.username == username ? 'text-primary' : 'text-success',
        ]"
        >{{message.author.username}}</strong>
        <small class="text-muted">{{humantime}}</small>
      </p>
      <p class="card-text">
        <rendered-text :text="message.text"></rendered-text>
      </p>
    </div>
  </div>
</template>
<script>
import moment from "moment";
export default {
  props: ["message", "username"],
  computed: {
    humantime: function() {
      if (null != this.message.time) {
        return moment(this.message.time).fromNow();
      } else {
        return "...";
      }
    }
  }
};
</script>