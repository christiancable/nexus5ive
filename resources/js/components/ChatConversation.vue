<template>
  <section v-if="errored">
    <div class="alert alert-primary">
      <p>We're sorry, we're not able to retrieve your list of chats at the moment, please try back later</p>
    </div>
  </section>
  <section v-else>
    <template v-if="chatID">
      <template v-if="!loading">
        <div class="chat-wrapper">
          <div class="chat-content d-flex flex-column justify-content-end" id="chat-content">
            <template v-if="chat.length > 0">
              <div class="chat-conversation">
                <div
                  v-for="message in chat"
                  v-bind:key="message.id"
                  :class="[ 'bg-transparent text-black py-2 px-3 mb-3 d-flex justify-content-between border-left', message.user.username == username ? 'border-primary' : 'border-success']"
                >
                  <span>{{message.text}}</span>
                  <small class="text-muted mx-3 d-none d-md-inline">{{message.time}}</small>
                </div>
              </div>
            </template>
            <template v-else>
              Start talking message
              <!-- {{-- @include('chat._new_conversation', [$currentPartner]) --}} -->
            </template>
          </div>
        </div>
        <chat-post v-bind:current-chat="chatID" v-on:sentMessage="refreshMessages"></chat-post>
      </template>
      <div v-else>
        <div class="spinner-border text-info" role="status">
          <span class="sr-only">Loading...</span>
        </div>
      </div>
    </template>
    <template v-else>
      <p>START NEW CHAT</p>
    </template>
  </section>
</template>

<script>
import { setInterval, clearInterval } from "timers";
export default {
  props: ["current-chat", "username"],

  data() {
    return {
      chat: [],
      chatID: "",
      loading: true,
      errored: false,
      interval: null,
      refresh_time: 5000
    };
  },

  computed: {},

  mounted() {
    if (this.currentChat !== void 0) {
      this.chatID = this.currentChat;
    }

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
      if (this.chatID != "") {
        axios
          .get("/chats/" + this.chatID)
          .then(response => {
            this.chat = response.data;
          })
          .catch(error => {
            this.errored = true;
          })
          .finally(() => (this.loading = false));
      } else {
        this.loading = false;
      }
    },

    refreshMessages: function(newMessage) {
      const sendingMessage = {
        id: "NEWMESSAGE",
        user: {
          username: this.username
        },
        text: newMessage,
        time: "..."
      };
      this.chat.push(sendingMessage);
      this.fetchData();
    }
  }
};
</script>