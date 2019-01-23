<template>
  <div class="card-carousel-wrapper">
    <div class="card-carousel--nav__left" @click="moveCarousel(-1)" :disabled="atHeadOfList"></div>

    <div class="card-carousel">
      <div class="card-carousel--overflow-container">
        <div class="card-carousel-cards" :style="{ transform: 'translateX' + '(' + currentOffset + 'px' + ')'}">

          <div class="card-carousel--card" v-for="item in items">
            <a href="{{ item.url }}">
              <img v-bind:src="item.image" />
              <div class="card-carousel--card--footer">
                <p>{{ item.label }}</p>
                <p>{{ item.tag }}</p>
              </div>
            </a>
          </div>

        </div>
      </div>
    </div>

    <div class="card-carousel--nav__right" @click="moveCarousel(1)" :disabled="atEndOfList"></div>
  </div>
</template>

<script>
  export default {
    name: "Cards",
    data () {
      return {
        currentOffset: 0,
        windowSize: 3,
        paginationFactor: 220,
      }
    },
    props: {
      items: {
        default: () => [],
        type: Array
      }
    },
    computed: {
      atEndOfList() {
        return this.currentOffset <= (this.paginationFactor * -1) * (this.items.length - this.windowSize);
      },
      atHeadOfList() {
        return this.currentOffset === 0;
      },
    },
    methods: {
      moveCarousel(direction) {
        // Find a more elegant way to express the :style. consider using props to make it truly generic
        if (direction === 1 && !this.atEndOfList) {
          this.currentOffset -= this.paginationFactor;
        } else if (direction === -1 && !this.atHeadOfList) {
          this.currentOffset += this.paginationFactor;
        }
      }
    }
  }
</script>
