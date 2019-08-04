<template>
  <div v-if="errored">
    <div class="alert alert-primary">
      <p>Sorry, there is no other people to talk to, please refresh the page</p>
    </div>
  </div>
  <div v-else>
    <div v-if="!loading">
      <h3>👋</h3>
      <p>Start a conversation.</p>

      <div class="dropdown d-inline">
        <button
          class="btn btn-secondary dropdown-toggle"
          type="button"
          id="chatUserSelectionMenuButton"
          data-toggle="dropdown"
          aria-haspopup="true"
          aria-expanded="false"
        >Chat to…</button>
        <div class="dropdown-menu" aria-labelledby="chatUserSelectionMenuButton">
          <a
            v-for="user in users"
            class="dropdown-item"
            :key="user"
            :href="'chat' + '/' + user"
          >{{user}}</a>
        </div>
      </div>
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