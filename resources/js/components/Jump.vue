<template>
  <ul class="nav navbar-nav">
    <li class="dropdown nav-item">
      <a
        href="#"
        class="dropdown-toggle nav-link"
        data-toggle="dropdown"
        role="button"
        aria-haspopup="true"
        aria-expanded="false"
        id="mentiondropdown"
      >
        <span class="oi oi-compass mr-1 text-success" aria-hidden="true"></span> Jump
      </a>

      <div class="dropdown-menu" aria-labelledby="mentiondropdown">
        <div class="form-group px-4 py-3">
          <label class="sr-only" for="topicFilter">Jump</label>
          <input
            v-model="searchTerm"
            class="form-control"
            id="topicFilter"
            placeholder="Search for a Section or Topic"
          >
        </div>
        <div role="separator" class="dropdown-divider"></div>
        <template v-if="matched.length!==0">
          <a
            class="dropdown-item"
            v-for="(item, index) in matched"
            :key="item.keyIndex"
            :value="item.keyIndex"
            :selected="index === 0"
            :href="[item.is_section ? '/section/' : '/topic/'] + item.id"
          >
            <template v-if="item.is_section">
              <span>
                <span class="oi oi-folder mr-1 text-primary" aria-hidden="true"></span>
                {{ item.title.length ? item.title : item.id + ' untitled'}}
              </span>
            </template>
            <template v-else>
              <span class="ml-3">
                <span class="oi oi-comment-square mr-1 text-muted" aria-hidden="true"></span>
                {{ item.title.length ? item.title : item.id + ' untitled'}}
              </span>
            </template>
          </a>
        </template>
        <template v-else>
          <span class="dropdown-item-text">
            <em>Nothing Found</em>
          </span>
        </template>
      </div>
    </li>
  </ul>
</template>


// v-bind:style= "[condition ? {styleA} : {styleB}]"
<script>
export default {
  data() {
    return {
      searchTerm: "",
      jumpDestinations: []
    };
  },

  computed: {
    matched: function() {
      var context = { searchTerm: this.searchTerm };
      return this.jumpDestinations.filter(function(item) {
        return (
          (item.title + item.intro)
            .toLowerCase()
            .indexOf(this.searchTerm.toLowerCase()) !== -1
        );
      }, context);
    }
  },

  mounted: function() {
    window.axios
      .get("/jump/")
      .then(response => {
        this.jumpDestinations = response.data;
      })
      .catch(error => {
        console.log("failed to get list of destinations");
      });
  }
};
</script>