<template>
  <div class="mode-toggle" @click="modeToggle" :class="darkDark">
    <div class="toggle">
      <div id="dark-mode" type="checkbox"></div>
    </div>
    <span v-html="label"></span>
  </div>
</template>

<script>
const CLASS_NIGHT_MODE = 'night';

export default {
  name: "darkMode",
  data() {
    return {
      darkMode: false,
      label: 'Light mode'
    }
  },
  methods: {
    dark: function() {

      [
        'body',
        '.header__notHome',
        '.header__search',
        '.bm-menu',
        '.Dso__main',
        '.Dso__title',
        '.Dso__description',
        'footer.footer'
      ].forEach(item => {
        if (null !== document.querySelector(item)) {
            document.querySelector(item).classList.add(CLASS_NIGHT_MODE);
        }
      });

      // Header + menu
      document.querySelectorAll('.header__menu__notHome').forEach(el => el.classList.add(CLASS_NIGHT_MODE));
      document.querySelectorAll('td').forEach(td => td.classList.add(CLASS_NIGHT_MODE));
      document.querySelectorAll('.breadcrumb a').forEach(link => link.classList.add(CLASS_NIGHT_MODE));
      // Left Menu
      document.querySelectorAll('span.bm-burger-bars').forEach(bar => bar.classList.add(CLASS_NIGHT_MODE))
      // Main
      document.querySelectorAll('article.card').forEach(el => el.classList.add(CLASS_NIGHT_MODE));

      // Form & inputs

      this.darkMode = true
      this.$emit('dark')
      this.label = 'Dark mode';
    },

    light: function() {
      document.querySelectorAll('.' + CLASS_NIGHT_MODE).forEach(el => {
        el.classList.remove(CLASS_NIGHT_MODE)
      })
      this.darkMode = false
      this.$emit('light')
      this.label = 'Light mode';
    },

    setMode: function(value) {
      if (CLASS_NIGHT_MODE === value) {
        localStorage.setItem('astro.otter.mode', value);
      } else {
        localStorage.clear();
      }
    },
    modeToggle() {
      if (this.darkMode || document.querySelector('body').classList.contains('night')) {
        this.setMode(null);
        this.light()
        this.label = 'Light mode';
      } else {
        this.setMode(CLASS_NIGHT_MODE);
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
  mounted() {
    if (CLASS_NIGHT_MODE === localStorage.getItem('astro.otter.mode')) {
      console.log('Init dark mode at load');
      this.dark()
    }
  }
}
</script>
