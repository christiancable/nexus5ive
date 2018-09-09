<template>
    <span v-html="renderedText">
    </span>
</template>

<script>
import marked from "marked";

export default {
  props: ["text"],
  computed: {
    renderedText() {
      return marked(this.text, { sanitize: true });
    }
  }
};
</script>