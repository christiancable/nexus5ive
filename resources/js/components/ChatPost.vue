<template>
  <form method="POST" action @submit.prevent="sendPost">
    <fieldset v-bind:disabled="loading">
      <div class="form-row">
        <div class="col-9 col-md-10">
          <label class="sr-only" for="text">Message</label>
          <textarea class="form-control" id="text" name="text" rows="3" v-model="message.text"></textarea>
        </div>
        <div class="col">
          <input class="btn btn-primary" type="submit" value="Send" />
        </div>
      </div>
    </fieldset>
  </form>
</template>
<script>
export default {
  props: ["current-chat"],
  data() {
    return {
      message: {
        text: ""
      },
      loading: false
    };
  },

  computed: {},

  mounted() {
    console.log("chat post form is mounted");
  },

  methods: {
    sendPost() {
      const data = this.message;
      this.loading = true;
      axios
        .post("", data)
        .then(response => {
          this.message.text = "";
          this.$emit("sentMessage");
          this.loading = false;
        })
        .catch(error => {
          console.log(error);
        });
    }
  }
};
</script>