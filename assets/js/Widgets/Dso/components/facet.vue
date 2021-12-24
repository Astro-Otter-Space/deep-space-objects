<template>
  <div class="checkbox-select">

    <div class="checkbox-select__trigger" :class="{ isActive: activeTrigger }" v-on:click="showDropdown()">
      <span class="checkbox-select__title">{{nameFacet}}</span>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 129 129"><path d="M121.3 34.6c-1.6-1.6-4.2-1.6-5.8 0l-51 51.1-51.1-51.1c-1.6-1.6-4.2-1.6-5.8 0-1.6 1.6-1.6 4.2 0 5.8l53.9 53.9c.8.8 1.8 1.2 2.9 1.2 1 0 2.1-.4 2.9-1.2l53.9-53.9c1.7-1.6 1.7-4.2.1-5.8z"/></svg>
    </div>

    <div id="dropdown" class="checkbox-select__dropdown" :class="{ activeSearch: showLoader }">
      <div class="checkbox-select__search-wrapp">
        <input type="text" @focus="showLoader = true" @blur="showLoader = false" placeholder="search..." v-model="search">
      </div>
      <div class="checkbox-select__col">
        <div class="checkbox-select__info">{{checkedFilters.length}} SELECTED</div>
      </div>
      <ul id="customScroll" class="checkbox-select__filters-wrapp" data-simplebar-auto-hide="false">
        <li v-for="(filter, index) in filters">
          <div class="checkbox-select__check-wrapp">
            <input :id="index" class="conditions-check" v-model="checkedFilters" :value="filter.code" type="checkbox">
            <label :for="index">{{filter.value}} ({{filter.number}})</label>
          </div>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import {fadeIn, fadeOut} from "../../../components/fade";

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
    },
    nameFacet: {
      default: '',
      type: String
    }
  },
  computed: {
    // filteredList() {
    //   return this.listItems.filter(item => {
    //     return item.toLowerCase().includes(this.search.toLowerCase());
    //   });
    // }
  },
  mounted() {
    this.filters = this.listItems
  },
  methods: {
    showDropdown: function() {
      if (!this.dropdown) {
        this.dropdown = true;
        this.activeTrigger = true;
        fadeIn(document.getElementById('dropdown'), 0.55);
        // TweenMax.fromTo("#dropdown", 0.55, { autoAlpha: 0, y: -10 }, { autoAlpha: 1, y: 0, ease: Power2.easeOut });
      } else {
        this.dropdown = false;
        this.activeTrigger = false;
        fadeOut(document.getElementById('dropdown'), 0.55);
        // TweenMax.to("#dropdown",0.2, {autoAlpha: 0,y: -10,ease: Power2.easeOut});
      }
    }
  }
}
</script>
