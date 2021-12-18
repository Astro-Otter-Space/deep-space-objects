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
        '.AppSearch__inputText',
        '.AppSearch__list ul',
        '.bm-menu',
        '.Dso__main',
        '.Dso__Form',
        '.Dso__description',
        'footer.footer'
      ].forEach(item => {
        if (null !== document.querySelector(item)) {
            document.querySelector(item).classList.add(CLASS_NIGHT_MODE);
        } else {
          console.log('Item "' + item + '" not found');
        }
      });

      [
        '.header__menu__notHome',
        '.breadcrumb a',
        'span.bm-burger-bars',
        '.header__drop_menu',
        '.header__search',
        '.AppSearch__list li a',
        '.Dso__title',
        'Dso__td',
        'article.card',
        '.Form__input',
        '.Form__select',
        '.Form__textare',
        'td',
        'a'
      ].forEach(item => {
        if (null !== document.querySelectorAll(item) && 0 < document.querySelectorAll(item).length) {
          document.querySelectorAll(item).forEach(el => el.classList.add(CLASS_NIGHT_MODE));
        }
      });

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
    // if (CLASS_NIGHT_MODE === localStorage.getItem('astro.otter.mode')) {
    //   console.log('Init dark mode at load');
    //   this.dark()
    // }
  }
}
</script>
