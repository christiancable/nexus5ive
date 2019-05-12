<template>
    <span v-html="renderedText">
    </span>
</template>

<script>
import marked from "marked";
import nxCode from "../nxcode";

export default {
  props: ["text"],
  computed: {
    renderedText() {
      return marked(nxCode.formatText(this.text), { sanitize: false });
    }
  }
};
</script>