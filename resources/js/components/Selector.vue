<template>
  <div class="container">
    <form>
      <div class="row mb-3">
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
      </div>

      <div class="row">
        <div class="col-1" v-if="label.length!=0">
          <label v-if="label.length!=0" class="my-1 mr-2" :for="label">{{label}}</label>
        </div>

        <div class="col">
          <input
            :id="label"
            type="text"
            class="form-control d-line"
            v-model="filterText"
            :placeholder="placeholder"
            :aria-label="itemValue"
            aria-describedby
          />

          <template v-if="(filteredItems.length!==0) && (filterText.length!==0)">
            <span class="list-group">
              <a
                v-for="item in filteredItems"
                v-bind:key="item[itemKey]"
                href="#"
                class="list-group-item"
                @click="addItem(item)"
              >{{item[itemValue]}}</a>
            </span>
          </template>
        </div>
      </div>
    </form>
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