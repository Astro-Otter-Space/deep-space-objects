<template>
  <div id="app" class="checkbox-select">

    <div id="dropdown" class="checkbox-select__dropdown" :class="{ activeSearch: showLoader }">
      <div class="checkbox-select__search-wrapp">
        <input type="text" @focus="showLoader = true" @blur="showLoader = false" placeholder="search..." v-model="search">
      </div>
      <div class="checkbox-select__col">
        <div class="checkbox-select__info">{{checkedFilters.length}} SELECTED</div>
      </div>
      <ul id="customScroll" class="checkbox-select__filters-wrapp" data-simplebar-auto-hide="false">
        <li v-for="(filter, index) in filteredList">
          <div class="checkbox-select__check-wrapp">
            <input :id="index" class="conditions-check" v-model="checkedFilters" :value="filter" type="checkbox">
            <label :for="index">{{filter}}</label>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: "facet",
  data() {
    return {
      filters: [],
      search: "",
      checkedFilters: [],
      activeTrigger: false,
      dropdown: false,
      showLoader: true
    }
  },
  props: {
    listItems: {
      default: () => [],
      type: Array
    }
  },
  computed: {
    filteredList() {
      return this.listItems.filter(item => {
        return item.toLowerCase().includes(this.search.toLowerCase());
      });
    }
  },
  mounted() {
    this.filters = this.listItems
  },
  methods: {
    showDropdown: () => {

    }
  }
}
</script>
