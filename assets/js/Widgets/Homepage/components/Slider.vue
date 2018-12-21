<template>
  <transition-group
    v-touch:swipe.right="next"
    v-touch:swipe.left="prev"
    :duration="1000"
    :style="{ paddingBottom: `${aspectRatio}%` }"
    tag="div"
    enter-active-class="AppSlider__enterActive"
    enter-class="AppSlider__enter"
    leave-active-class="AppSlider__leaveActive"
    leave-to-class="AppSlider__leaveTo"
    class="AppSlider__slides"
  >
    <div
      v-for="(image, index) in images"
      v-bind:key="image"
      :style="{ backgroundImage: `url(${image})` }"
      v-show="activeIndex === index"
      class="AppSlider__dImage"
    ></div>
  </transition-group>
</template>

<script>
  export default {
    name: "Slider",
    props: {
      height: {
        default: 1920,
        type: Number
      },
      images: {
        default: () => [],
        type: Array
      },
      interval: {
        default: 10000,
        type: Number
      },
      width: {
        default: 3072,
        type: Number
      },
      subTitle: {
        default: "",
        type: String
      }
    },
    data() {
      return {
        activeIndex: 0,
        paused: false,
        time: this.interval
      };
    },
    computed: {
      aspectRatio() {
        return (this.height / this.width) * 100;
      }
    },
    created() {
      this.startInterval();
    },
    methods: {
      goToIndex(index) {
        this.activeIndex = index;
        this.time = this.interval;
      },
      next() {
        let nextIndex = this.activeIndex + 1;
        if (!this.images[nextIndex]) {
          nextIndex = 0;
        }
        this.goToIndex(nextIndex);
      },
      prev() {
        let nextIndex = this.activeIndex - 1;
        if (!this.images[nextIndex]) {
          nextIndex = this.images.length - 1;
        }
        this.goToIndex(nextIndex);
      },
      startInterval() {
        const precision = 100;
        const clock = setInterval(() => {
          if (!this.paused) this.time -= precision;
          if (this.time <= 0) this.next();
        }, precision);

        this.$once(`hook:destroyed`, () => clearInterval(clock));
      }
    }
  };
</script>
