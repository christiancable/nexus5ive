<template>
  <div class="container" v-if="topics.length !== 0">
    <form class="form-inline">
      <div class="input-group row">
        <label class="sr-only" for="topicFilter">Topic</label>
        <input
          id="topicFilter"
          v-model="searchTerm"
          class="form-control col-md-7"
          placeholder="Search for a topic"
          autofocus
        >
        <template v-if="matchedTopics.length!==0">
          <div class="input-group-append col-md-5">
            <select v-on="{change:go}" @keyup.enter="go" class="custom-select">
              <option
                v-for="(topic, index) in matchedTopics"
                :key="topic.id"
                :value="topic.id"
                :selected="index === 0"
              >{{ topic.title.length ? topic.title : topic.id + ' untitled'}}</option>
            </select>
          </div>
        </template>
      </div>
    </form>
  </div>
  <div v-else>
    <span>Loading...</span>
  </div>
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

  methods: {
    go: function(ev) {
      console.log("go to topic " + ev.target.value);
      location.replace("/topic/" + ev.target.value);
    }
  },

  mounted: function() {
    console.log("getting topics");
    window.axios
      .get("/topic/")
      .then(response => {
        this.topics = response.data;
      })
      .error(error => {
        console.log("failed to get list of topics");
      });
  }
};
</script>