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
        <span class="oi oi-comment-square mr-1" aria-hidden="true"></span> Topics
      </a>

      <div class="dropdown-menu" aria-labelledby="mentiondropdown">
        <div class="form-group px-4 py-3">
          <label class="sr-only" for="topicFilter">Topic</label>
          <input
            v-model="searchTerm"
            class="form-control"
            id="topicFilter"
            placeholder="Search for a Topic"
          >
        </div>
        <div role="separator" class="dropdown-divider"></div>
        <template v-if="matchedTopics.length!==0">
          <a
            class="dropdown-item"
            v-for="(topic, index) in matchedTopics"
            :key="topic.id"
            :value="topic.id"
            :selected="index === 0"
            :href="'/topic/' + topic.id"
          >{{ topic.title.length ? topic.title : topic.id + ' untitled'}}</a>
        </template>
        <template v-else>
          <span class="dropdown-item-text">
            <em>No Topics Found</em>
          </span>
        </template>
      </div>
    </li>
  </ul>
</template>



<script>
export default {
  data() {
    return {
      searchTerm: "",
      topics: []
    };
  },

  computed: {
    matchedTopics: function() {
      var context = { searchTerm: this.searchTerm };
      return this.topics.filter(function(topic) {
        return (
          (topic.title + topic.intro)
            .toLowerCase()
            .indexOf(this.searchTerm.toLowerCase()) !== -1
        );
      }, context);
    }
  },

  mounted: function() {
    window.axios
      .get("/topic/")
      .then(response => {
        this.topics = response.data;
      })
      .catch(error => {
        console.log("failed to get list of topics");
      });
  }
};
</script>