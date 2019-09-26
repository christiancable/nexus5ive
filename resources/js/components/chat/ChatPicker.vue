<template>
  <div v-if="errored">
    <div class="alert alert-primary">
      <p>Sorry, there is no other people to talk to, please refresh the page</p>
    </div>
  </div>
  <div v-else>
    <div v-if="!loading">
      <button type="button" class="btn btn-outline-primary" @click="toggleList">
        <span class="oi oi-plus mr-3"></span> New
      </button>
      <div v-if="display">
        <div class="form-group">
          <label for="chatPartnersList">BETTER MICROCOPY HEERE</label>
          
          <selector :itemKey="itemKey" :itemValue="itemValue" :items="users"></selector>
            
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
      potentialUsers: [],
      selectedUsers: [],
      itemKey: 'id',
      itemValue: 'username',
      loading: true,
      display: false,
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
    },

    toggleList: function() {
      if (this.display) {
        this.display = false;
      } else {
        this.display = true;
      }
    }
  }
};
</script>