<template>
    <span v-html="post"></span>
</template>

<script>
    export default {
        props: ['post'],
    }
</script>