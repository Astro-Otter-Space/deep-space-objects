<template>
  <div class="checkbox-select">

    <div class="checkbox-select__trigger" :class="{ isActive: activeTrigger }" v-on:click="showDropdown($event)">
      <span class="checkbox-select__title">{{nameFacet}}</span>
      <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 129 129">
        <path d="M121.3 34.6c-1.6-1.6-4.2-1.6-5.8 0l-51 51.1-51.1-51.1c-1.6-1.6-4.2-1.6-5.8 0-1.6 1.6-1.6 4.2 0 5.8l53.9 53.9c.8.8 1.8 1.2 2.9 1.2 1 0 2.1-.4 2.9-1.2l53.9-53.9c1.7-1.6 1.7-4.2.1-5.8z"/>
      </svg>
    </div>

    <div data-type="dropdown" class="checkbox-select__dropdown" :class="{ activeSearch: showLoader }">
      <div class="checkbox-select__search-wrapp">
        <input type="search" @focus="showLoader = true" @blur="showLoader = false" placeholder="search..." v-model="search" class="Form__input">
      </div>

      <ul id="customScroll" class="Form__list checkbox-select__filters-wrapp" data-simplebar-auto-hide="false">
        <li v-for="(filter, index) in filteredList" class="appGridFacet__item" v-show="0 < filter.number">
          <label class="appGridFilter__btn-radio">
            <input type="radio"
                   name="appGridFilter__radio-grp"
                   v-model="checkedFilters"
                   :data-url="filter.full_url"
                   :id="filter.value"
                   :value="filter.value"
                   v-on:change="filteringByUrl"
            />
            <svg width="20px" height="20px" viewBox="0 0 20 20">
              <circle cx="10" cy="10" r="9"></circle>
              <path d="M10,7 C8.34314575,7 7,8.34314575 7,10 C7,11.6568542 8.34314575,13 10,13 C11.6568542,13 13,11.6568542 13,10 C13,8.34314575 11.6568542,7 10,7 Z" class="inner"></path>
              <path d="M10,1 L10,1 L10,1 C14.9705627,1 19,5.02943725 19,10 L19,10 L19,10 C19,14.9705627 14.9705627,19 10,19 L10,19 L10,19 C5.02943725,19 1,14.9705627 1,10 L1,10 L1,10 C1,5.02943725 5.02943725,1 10,1 L10,1 Z" class="outer"></path>
            </svg>
            <span>{{ filter.value }} ({{filter.number}})</span>
          </label>
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
import * as slide from "../../../components/slide";

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
    filteredList() {
      return this.listItems.filter(item => {
        return item.value.toLowerCase().includes(this.search.toLowerCase());
      });
    }
  },
  mounted() {
    this.filters = this.listItems
  },
  methods: {
    showDropdown: function(event) {

      let targetDropdown = event.target
                            .closest('div.checkbox-select')
                            .querySelector('[data-type="dropdown"]');

      if (null !== targetDropdown) {
        if (!this.dropdown) {
          this.dropdown = true;
          this.activeTrigger = true;
        } else {
          this.dropdown = false;
          this.activeTrigger = false;
        }
        slide.toggle(targetDropdown, 500);
      }
    },
    filteringByUrl: function(event) {
      let inputRadio = event.target;
      let url = inputRadio.dataset.url;
      window.location.href = url;
    }
  }
}
</script>
