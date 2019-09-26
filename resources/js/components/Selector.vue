<template>
  <div class="d-inline">
    <form class="form-inline">
      <label v-if="label.length!=0" class="my-1 mr-2" for="inlineFormCustomSelectPref">{{label}}</label>
      <span>
        <a
          href="#"
          v-for="item in chosenItems"
          v-bind:key="item[itemKey]"
          class="badge badge-pill badge-primary mr-1"
          @click="removeItem(item)"
        >
          {{item[itemValue]}}
          <span>x</span>
        </a>
      </span>

      <input
        type="text"
        class="form-control d-line"
        v-model="filterText"
        :placeholder="placeholder"
        :aria-label="itemValue"
        aria-describedby
      />
    </form>

    <template v-if="(filteredItems.length!==0) && (filterText.length!==0)">
      <ul class="nav flex-column">
        <li v-for="item in filteredItems" :class="nav-item" v-bind:key="item[itemKey]">
          <a href="#" class="nav-link" @click="addItem(item)">{{item[itemValue]}}</a>
        </li>
      </ul>
    </template>
  </div>
</template>

<script>
export default {
  props: {
    placeholder: {
      type: String,
      default: ""
    },
    itemKey: {
      type: String,
      default: "key"
    },
    itemValue: {
      type: String,
      default: "value"
    },
    items: {
      type: Array,
      default: []
    },
    label: {
      type: String,
      default: ""
    }
  },
  data() {
    return {
      filterText: "",
      avaliableItems: [],
      chosenItems: []
    };
  },

  computed: {
    filteredItems: function() {
      var context = {
        searchTerm: this.filterText,
        itemValue: this.itemValue
      };

      return this.items.filter(function(item) {
        return item[this.itemValue]
          .toLowerCase()
          .includes(this.searchTerm.toLowerCase());
      }, context);
    }
  },

  mounted() {
    this.avaliableItems = this.items;
  },

  methods: {
    addItem: function(item) {
      // remove from avaliable items
      var index = this.avaliableItems.indexOf(item);
      if (index > -1) {
        this.avaliableItems.splice(index, 1);
      }

      // add to chosen items
      this.chosenItems.push(item);
      this.resetFilter();
    },

    removeItem: function(item) {
      // remove from chosen items
      var index = this.chosenItems.indexOf(item);
      if (index > -1) {
        this.chosenItems.splice(index, 1);
      }

      // add to avaliable items
      this.avaliableItems.push(item);
      this.resetFilter();
    },

    resetFilter: function() {
      this.filterText = "";
    }
  }
};
</script>