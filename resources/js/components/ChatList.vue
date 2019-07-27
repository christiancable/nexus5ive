<template>
  <section v-if="errored">
    <div class="alert alert-primary">
      <p>We're sorry, we're not able to retrieve your list of chats at the moment, please try back later</p>
    </div>
  </section>
  <section v-else>
    <nav class="list-group" v-if="!loading">
      <a
        v-for="chat in chats"
        v-bind:key="chat.username"
        :class="[ 'list-group-item list-group-item-action d-flex justify-content-between', chat.username == currentChat ? 'active' : '']"
        :href="'/chat/' + chat.username"
      >
        {{ chat.username }}
        <span
          class="badge badge-success ml-1"
          v-if="((chat.username != currentChat) & (chat.unread != 0))"
        >{{ chat.unread }}</span>
      </a>
    </nav>
    <nav v-else>
      <div class="spinner-border text-info" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </nav>
  </section>
</template>

<script>
import { setInterval, clearInterval } from "timers";
export default {
  props: ["current-chat"],

  data() {
    return {
      chats: [],
      loading: true,
      errored: false,
      interval: null,
      refresh_time: 5000
    };
  },

  computed: {},

  mounted() {
    this.fetchData();

    this.interval = setInterval(
      function() {
        this.fetchData();
      }.bind(this),
      this.refresh_time
    );
  },

  beforeDestroy: function() {
    clearInterval(this.interval);
  },

  methods: {
    fetchData: function() {
      axios
        .get("/chats")
        .then(response => {
          this.chats = response.data;
        })
        .catch(error => {
          this.errored = true;
        })
        .finally(() => (this.loading = false));
    }
  }
};
</script>