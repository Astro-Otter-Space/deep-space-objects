<template>
  <div>
    <div class="mode-toggle" @click="modeToggle" :class="darkDark">
      <div class="toggle">
        <div id="dark-mode" type="checkbox"></div>
      </div>

    </div>

    <div>
      <span v-html="label"></span>
    </div>
  </div>

</template>

<script>
const CLASS_NIGHT_MODE = 'night';

import {default as mode} from './../../components/night_mode';

export default {
  name: "darkMode",
  data() {
    return {
      darkMode: (CLASS_NIGHT_MODE === localStorage.getItem('astro.otter.mode')) || false,
      label: 'Light mode'
    }
  },
  methods: {
    dark: function() {
      mode.nightMode();

      this.darkMode = true
      this.$emit('dark')
      this.label = 'Dark mode';
    },

    light: function() {
      mode.dayMode();
      this.darkMode = false
      this.$emit('light')
      this.label = 'Light mode';
    },
    /**
     * Toggle
     */
    modeToggle() {
      if (this.darkMode || document.querySelector('body').classList.contains('night')) {
        this.light()
        this.label = 'Light mode';
      } else {
        this.dark();
        this.label = 'Dark mode';
      }
    },
  },
  computed: {
    darkDark() {
      return this.darkMode && 'darkmode-toggled'
    }
  },
  /*watch: {

  },*/
  mounted() {
    // Apply dark mode on mount
    if (CLASS_NIGHT_MODE === localStorage.getItem('astro.otter.mode')) {
      this.dark();

      // for DOM element not mounted like Dso__main etc...
      document.onreadystatechange = () => {
        if ("complete" === document.readyState ) {
          this.dark();
        }
      }
    }
  }
}
</script>
