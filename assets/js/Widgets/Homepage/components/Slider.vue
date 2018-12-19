<template>
  <div class="AppSlider">
    <transition-group
      :duration="1000"
      :style="{ paddingBottom: `${aspectRatio}%` }"
      tag="div"
      v-touch:swipe.right="next"
      v-touch:swipe.left="prev"
      enter-active-class="AppSlider__enterActive"
      enter-class="AppSlider__enter"
      leave-active-class="AppSlider__leaveActive"
      leave-to-class="AppSlider__leaveTo"
      class="AppSlider__slides"
    >
      <img
        v-for="(image, index) in images"
        v-show="activeIndex === index"
        :key="index"
        :src="image"
        class="AppSlider__image"
        alt=""
      />
    </transition-group>
  </div>
</template>

<script>
  export default {
    name: "Slider",
    props: {
      titre: "Deep Space Objects",
      height: {
        default: 1080,
        type: Number
      },
      interval: {
        default: 10000,
        type: Number
      },
      width: {
        default: 1920,
        type: Number
      },
      images: {
        default: () => [],
        type: Array
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
          if (0 >= this.time) this.next();
        }, precision);

        this.$once("hook:destroyed", () => clearInterval(clock));
      }
    }
  };
</script>
