<template>
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
      <span class="oi oi-magnifying-glass mr-1" aria-hidden="true"></span> Search
    </a>

    <div class="dropdown-menu" aria-labelledby="mentiondropdown">
      <div class="px-4 py-3" action="/search" method="post">
        <slot></slot>
        <div class="form-group">
          <label class="sr-only" for="topicFilter">Search</label>
          <input
            v-model="searchTerm"
            class="form-control"
            id="topicFilter"
            placeholder="Search for a Section or Topic"
            v-on:keyup.enter="performSearch"
          >
        </div>
        <div class="form-check">
          <input type="checkbox" class="form-check-input" id="showRecent" v-model="showRecent">
          <label class="form-check-label" for="showRecent">Only recent topics</label>
        </div>
      </div>
      <div role="separator" class="dropdown-divider"></div>
      <template v-if="locations.length!==0">
        <a
          class="dropdown-item"
          v-for="(item, index) in locations"
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
        <a :href="'/search/' + searchTerm" class="dropdown-item">
          <span class="oi oi-magnifying-glass" aria-hidden="true"></span>
          Search for
          <em>{{ searchTerm }}</em>
        </a>
      </template>
    </div>
  </li>
</template>

<script>
export default {
  data() {
    return {
      searchTerm: "",
      showRecent: true,
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
    },

    recent: function() {
      return this.matched.filter(function(item) {
        return true == item.is_recent;
      });
    },

    locations: function() {
      if (true == this.showRecent) {
        return this.recent;
      } else {
        return this.matched;
      }
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

    /* hide all vue fall back markup */
    let elementsToHide = document.getElementsByClassName("replace-with-vue");
    for (let i = 0; i < elementsToHide.length; ++i) {
      elementsToHide[i].style.display = "none";
    }
  },

  methods: {
    performSearch: function() {
      if (this.searchTerm.length > 0) {
        window.location = "/search/" + this.searchTerm;
      }
    }
  }
};
</script>