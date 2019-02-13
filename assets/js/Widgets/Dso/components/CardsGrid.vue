<template>
  <div id="appGrid">
    <div v-if="showControls == true">
      <ul>
        <li v-for="control in listControls">
          <input type="radio"
            name="selectItem"
            v-model="itemselect"
            :id="control.value"
            :value="control.value"
          /> <label :for="control.value">{{ control.label }}</label>
        </li>
      </ul>
    </div>

    <transition-group tag="main" name="card">
      <article v-for="(item, index) in items" :key="index + 0" class="card" v-show="(itemselect === item.filter) || (itemselect == 1)">
        <a v-bind:href="item.url" target="_blank">
          <div class="image">
            <img :src="item.image" :alt="item.value" v-on:load="isLoaded()" v-bind:class="{ active: isActive }">
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
</template>

<script>
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
