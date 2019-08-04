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
          <div
            class="chat-content d-flex flex-column justify-content-end"
            id="chat-content"
            v-chat-scroll
          >
            <template v-if="chat.length > 0">
              <div class="chat-conversation">
                <chat-message
                  v-for="message in chat"
                  v-bind:key="message.id"
                  :message="message"
                  :username="username"
                ></chat-message>
              </div>
            </template>
            <template v-else>
              <chat-start v-bind:current-chat="chatID"></chat-start>
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
      <chat-new></chat-new>
    </template>
  </section>
</template>

<script>
import VueChatScroll from "vue-chat-scroll";
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
      refresh_time: 5 * 1000
    };
  },

  computed: {},

  mounted() {
    if (this.currentChat !== void 0) {
      this.chatID = this.currentChat;
    }
    this.fetchData();
    this.resetTimer();
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
        author: {
          username: this.username
        },
        text: newMessage,
        time: null
      };
      this.chat.push(sendingMessage);
      this.fetchData();
      this.resetTimer();
    },

    resetTimer: function() {
      clearInterval(this.interval);
      this.interval = setInterval(
        function() {
          this.fetchData();
        }.bind(this),
        this.refresh_time
      );
    }
  }
};
</script>