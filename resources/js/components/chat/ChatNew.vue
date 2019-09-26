<template>
  <div v-if="errored">
    <div class="alert alert-primary">
      <p>Sorry, there is no other people to talk to, please refresh the page</p>
    </div>
  </div>
  <div v-else>
    <div v-if="!loading">
      <selector :itemKey="itemKey" :itemValue="itemValue" :items="users" :label="'To:'"></selector>
    </div>
    <div v-else>
      <div class="spinner-border text-info" role="status">
        <span class="sr-only">Loading...</span>
      </div>
    </div>
  </div>
</template>
<script>
export default {
  data() {
    return {
      itemKey: "id",
      itemValue: "username",
      users: [],
      loading: true,
      errored: false
    };
  },

  computed: {},

  mounted() {
    this.fetchData();
  },

  methods: {
    fetchData: function() {
      axios
        .get("/chatsusers")
        .then(response => {
          this.users = response.data;
        })
        .catch(error => {
          this.errored = true;
        })
        .finally(() => (this.loading = false));
    }
  }
};
</script>