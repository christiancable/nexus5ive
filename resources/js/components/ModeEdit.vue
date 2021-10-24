<template>
  <span>
    <div class="form-group">
      <label>Mode</label>
      <select name="mode" class="custom-select" id="mode" v-model="selected_mode_id">
        <option v-for="mode in modes" :key="mode.id" :value="mode.id">
          {{ mode.name }}
        </option>
      </select>
    </div>

    <div class="form-group">
      <label for="welcome">Login Screen Welcome</label>
      <textarea
        class="form-control"
        id="welcome"
        name="welcome"
        cols="40"
        rows="5"
        v-model="modes[selected_mode_id]['welcome']"
      ></textarea>
    </div>

    <div class="form-group">
      <label>Mode theme</label>
      <select class="form-control custom-select" name="theme_id" v-model="modes[selected_mode_id]['theme_id']">
        <option v-for="theme in themes" :key="theme.id" :value="theme.id">{{ theme.name }}</option>
      </select>
    </div>

    <div class="form-group">
      <label for="theme_override">All users use this theme</label>
      <input
        name="theme_override"
        id="theme_override"
        type="checkbox"
        v-model="modes[selected_mode_id]['override']"
      />
    </div>
  </span>
</template>

<script>
export default {
  props: ["modes", "current_mode_id", "themes"],
  mounted() {
    this.selected_mode_id = this.current_mode_id;
  },
  data() {
    return {
      selected_mode_id: "",
    };
  },
};
</script>
