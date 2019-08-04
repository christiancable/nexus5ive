<template>
  <div class="alert alert-success" role="alert">
    <p>
      👋 New chat with
      <strong>{{currentChat}}</strong>
    </p>

    <p>
      Say
      <q>Hi!</q>
    </p>
  </div>
</template>
<script>
export default {
  props: ["current-chat"],
  data() {
    return {};
  },

  computed: {},

  mounted() {},

  methods: {}
};
</script>