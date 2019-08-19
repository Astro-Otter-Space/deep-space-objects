<template>
  <div>
    <div class="appGridFilter" v-if="showControls == true">
      <div class="appGridFilter__filter">
        <label v-for="control in listControls" class="appGridFilter__btn-radio">
          <input type="radio"
                 name="appGridFilter__radio-grp"
                 v-model="itemselect"
                 :id="control.value"
                 :value="control.value"
          />
          <svg width="20px" height="20px" viewBox="0 0 20 20">
            <circle cx="10" cy="10" r="9"></circle>
            <path d="M10,7 C8.34314575,7 7,8.34314575 7,10 C7,11.6568542 8.34314575,13 10,13 C11.6568542,13 13,11.6568542 13,10 C13,8.34314575 11.6568542,7 10,7 Z" class="inner"></path>
            <path d="M10,1 L10,1 L10,1 C14.9705627,1 19,5.02943725 19,10 L19,10 L19,10 C19,14.9705627 14.9705627,19 10,19 L10,19 L10,19 C5.02943725,19 1,14.9705627 1,10 L1,10 L1,10 C1,5.02943725 5.02943725,1 10,1 L10,1 Z" class="outer"></path>
          </svg>
          <span>{{ control.label }}</span>
        </label>
      </div>
    </div>

    <div id="appGrid">
      <transition tag="facets" name="list-facets">
        <div class="appGridFacet__list" v-if="showFacets && 0 < Object.keys(listFacets).length">
          <ul v-for="facets in listFacets">

            <h3 class="Dso__title">
              {{facets.name}}
              <a v-bind:href="facets.delete_url" v-if="facets.delete_url" class="appGridFacet__removeFilter" title="Remove filters">
                <svgicon name="cross" width="20" height="20"></svgicon>
              </a>
            </h3>

            <ul v-for="facet in facets.list" v-if="0 < facets.list.length">
              <li class="appGridFacet__item">
                <a v-bind:href="facet.full_url" v-bind:title="facet.value">{{facet.value}} <span class="appGridFacet__badge">{{facet.number}}</span></a>
              </li>
            </ul>
          </ul>
        </div>
      </transition>

      <transition-group tag="main" name="card">
        <article v-for="(item, index) in items" :key="index + 0" class="card" v-show="(itemselect === item.filter) || (itemselect == 1)">
          <a v-bind:href="item.url" v-bind:title="item.value">
            <div class="image">
              <figure>
                <img :src="item.image" :alt="item.value" v-on:load="isLoaded()" v-bind:class="{ active: isActive }">
                <figcaption v-if="item.user !== undefined && 0 < item.user.length">Astrobin Image by {{item.user}}</figcaption>
              </figure>
            </div>
            <div class="description">
              <span class="playcount">
                <span v-bind:style="{width: m_percentage(33) + '%'}"></span>
              </span>
              <h3 class="title" :data-id="item.value">{{ item.value }}</h3>
              <p class="artist">{{ item.label }}</p>
            </div>
          </a>
        </article>
      </transition-group>
    </div>
  </div>
</template>

<script>
  import axios from 'axios';

  export default {
    name: "CardsGrid",
    data() {
      return {
        isActive: false,
        maxPlayCount: 0,
        gridGap: 30,
        gridMin: 175,
        gridItems: 20,
        itemselect: 1
      }
    },
    props: {
      items: {
        default: () => [],
        type: Array
      },
      showControls: {
        default: false,
        type: Boolean
      },
      listControls: {
        default: () => [],
        type: Array
      },
      listFacets: {
        default: () => ({}),
        type: Object
      },
      showFacets: {
        default: false,
        type: Boolean
      },
      urlAjaxData: {
          default: '',
          type: string
      }
    },
    methods: {
      isLoaded: function() {
        this.isActive = true;
      },
      m_percentage: function(value) {
        return parseInt((value * 100) / this.$data.maxPlayCount);
      },
      changeGridGap: function() {
        document.querySelector('main').style.setProperty('--grid-gap', this.gridGap + 'px');
      },
      changeGridMin: function() {
        document.querySelector('main').style.setProperty('--grid-min', this.gridMin + 'px');
      },
      changeGridItems: function() {
        var gridItemSetting = this.gridItems;
        if (this.gridItems == 0) {
          gridItemSetting = 'auto-fill';
        }
        document.querySelector('main').style.setProperty('--grid-items', gridItemSetting);
      },
      getDataAjax: function() {
        // https://vuejsdevelopers.com/2017/08/28/vue-js-ajax-recipes/
        // In url, do not forget filter on dso type if selected
        axios.get(this.urlAjaxData, {params: {start: 20, offset: 20}})
            .then((response) => {
                this.items = response.data;
            })
            .catch(err => {

            })
      }
    },
    computed: {

    },
    filters: {
      percentage: function(value) {
        return parseInt((value * 100) / this.$data.maxPlayCount);
      }
    }
  }
</script>
