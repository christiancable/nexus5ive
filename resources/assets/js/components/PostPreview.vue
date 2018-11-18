<template>
    <div class="card mb-3" :id="post.id">
        <div class="card-header bg-primary text-white" v-if="post.title">
            <span class="card-title">{{post.title}}</span>
        </div>
        <div class="card-body">
            <p class="card-text">
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