<template>
<div class="panel panel-info">
    <div class="panel-heading" v-if="post.title">
        <h3 class="panel-title" id="preview-title">{{post.title}}</h3>
    </div>

    <div class="panel-body">
        <p id="preview-view">
            <rendered-text :text="post.text"></rendered-text>
        </p>
    </div>
</div>
</template>

<script>
import marked from "marked";
export default {
  props: ["post"]
};
</script>