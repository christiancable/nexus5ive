<template>
  <div class="card bg-transparent chat-message">
    <div class="card-body pl-1 pt-0 pb-1">
      <p class="card-title d-flex justify-content-between">
        <strong :class="['text-' + style]">{{message.author.username}}</strong>
        <small class="text-muted">{{humantime}}</small>
      </p>
      <p :class="[
        'card-text pl-3 border-left',
         'border-' + style]">
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
    },
    style: function() {
      if (this.message.author.username == this.username) {
        return "primary";
      } else {
        return "success";
      }
    }
  }
};
</script>