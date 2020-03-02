<template>
  <header v-bind:class="[ !this.isHome  ? 'header__notHome': '', 'header']">

    <!--Slide :burgerIcon="false" disableOutsideClick ref="slideMenu" width="300">
      <a v-for="menu in leftSideMenu" v-bind:href="menu.path" v-bind:title="menu.label">
        <svgicon v-bind:name="menu.icon_class" width="30" height="30" color="#e9e9e9"></svgicon>
        <span>{{menu.label}}</span>
      </a>
    </Slide -->

    <div class="header__wrap">
      <span class="h1 h1__title">
        <!-- Open Menu-->
        <!-- span v-on:click="openSlideMenu" class="header__barSlideMenu" v-bind:title="titleOpenMenu">
          <svgicon name="bars" width="30" height="30"></svgicon>
        </span -->
        <a v-bind:href="homepageRoute" v-bind:title="title">
          <span itemprop="name">{{title}}</span>
        </a>
      </span>

      <nav id="headerMenu" v-bind:class="[ !this.isHome  ? 'header__menu__notHome': '', 'header__menu']">
        <!--Search-->
        <li v-if="!this.isHome">
          <a v-on:click="displaySearch(hide)" v-bind:title="searchPlaceholder">
            <svgicon name="search" width="30" height="30" color="#e9e9e9"></svgicon>
          </a>
        </li>

        <!-- data -->
        <li class="header__drop">
          <a v-on:click="displayDropMenu" v-bind:title="titleData">
            <svgicon name="galaxy-cluster" width="30" height="30" color="#e9e9e9"></svgicon>
          </a>
          <ul class="header__drop_menu">
            <li v-for="menu in menuData">
              <a v-bind:href="menu.path" v-bind:title="menu.label">
                <svgicon v-bind:name="menu.icon_class" width="20" height="20" original></svgicon>
                <span>{{ menu.label }}</span>
              </a>
            </li>
          </ul>
        </li>

        <!-- Observations -->
        <li class="header__drop">
          <a v-on:click="displayDropMenu" v-bind:title="titleObservation">
            <svgicon name="telescop" width="30" height="30" color="#e9e9e9"></svgicon>
          </a>
          <ul class="header__drop_menu">
            <li v-for="menu in menuObservations" >
              <a v-bind:href="menu.path" v-bind:title="menu.label">
                <svgicon v-bind:name="menu.icon_class" width="20" height="20" original></svgicon>
                <span>{{ menu.label }}</span>
              </a>
            </li>
          </ul>
        </li>

        <!-- Dark/day mod -->
        <!-- li>
          <a v-on:click="switchTheme(theme)" v-bind:title="titleSwitchMode">
            <svgicon v-bind:name="theme" width="30" height="30" color="#e9e9e9"></svgicon>
          </a>
        </li -->

        <!--Languages-->
        <li class="header__drop">
          <a v-on:click="displayDropMenu" v-bind:title="titleSwitchLang">
            <svgicon name="globe" width="30" height="30" color="#e9e9e9"></svgicon>
            <svgicon v-bind:name="currentFlag" width="15" height="15" original class="floatFlag"></svgicon>
          </a>
          <ul class="header__drop_menu">
            <li v-for="locale in listLocales">
              <a v-bind:href="locale.path" v-bind:hreflang="locale.locale" :key="locale.locale" v-bind:title="locale.label" rel="alternate">
                <svgicon v-bind:name="locale.flag" width="20" height="20" original></svgicon>
                <span>{{ locale.label }}</span>
              </a>
            </li>
          </ul>
        </li>
      </nav>
    </div>

    <div class="header__search" v-if="hide">
      <transition>
        <searchautocomplete
          ref="search"
          :searchPlaceholder="searchPlaceholder"
          :customClasses="autoCompleteClasse"
          :url="searchUrl"
          id="search"
        ></searchautocomplete>
      </transition>
    </div>
  </header>
</template>

<script>
  let homeRoute = document.getElementById('appHeader').dataset.homeRoute;
  // let leftSideMenu = JSON.parse(document.getElementById('appHeader').dataset.menuSide);
  let menuData = JSON.parse(document.getElementById('appHeader').dataset.menuData);
  let menuObservations = JSON.parse(document.getElementById('appHeader').dataset.menuObservation);
  let routeSf = document.getElementById('appHeader').dataset.route;
  let listLocales = JSON.parse(document.getElementById('appHeader').dataset.locales);
  let currentLocale = document.getElementById('appHeader').dataset.currentlocale;
  let urlSearch = document.getElementById('appHeader').dataset.searchRoute;
  let labelsTrans = JSON.parse(document.getElementById('appHeader').dataset.labels);
  // let titleNightMode = labelsTrans.nightMode;
  // let titleDayMode = labelsTrans.dayMode;

  import searchautocomplete from './../Homepage/components/Searchautocomplete';
  // import { Slide } from 'vue-burger-menu';
  import './../Icons/index';

  window.addEventListener("resize", function(event) {
    closeAllMenu();
  });

  let KEY_THEME = 'theme';
  var themeLocalStorage = {
    fetch: function() {
      return localStorage.getItem(KEY_THEME) || 'moon';
    },
    save: function(theme) {
      localStorage.setItem(KEY_THEME, theme);
    }
  };

  export default {
    name: "Header",
    components: {
      searchautocomplete
      // Slide
    },
    data() {
      return {
        homepageRoute: homeRoute,
        menuData: menuData,
        menuObservations: menuObservations,
        title: labelsTrans.title,
        listLocales: listLocales,
        currentLocale: currentLocale,
        currentFlag: 'flag_en',
        theme: themeLocalStorage.fetch(),
        currentRoute: routeSf,
        homeRoute: 'homepage',
        hide: false,
        autoCompleteClasse: {
          wrapper: 'AppHeader__wrapper',
          input: 'AppSearch__inputText',
          list: 'AppHeader__list'
        },
        searchPlaceholder: labelsTrans.searchPlaceholder,
        searchUrl: urlSearch,
        titleOpenMenu: labelsTrans.openMenu,
        titleSwitchLang: labelsTrans.switchLang,
        // titleSwitchMode: labelsTrans.nightMode,
        titleData: labelsTrans.titleData,
        titleObservation: labelsTrans.titleObservation
      }
    },
    watch: {
      theme: {
        handler: function(newTheme) {
          themeLocalStorage.save(newTheme)
        }
      }
    },
    methods: {
      displayDropMenu: function(event) {
        if (event) {
          let item = null;
          if("a" === event.target.localName) {
            item = event.target.parentElement;

          } else if ("svg" === event.target.localName) {
            item = event.target.parentElement.parentElement;

          } else if("path" === event.target.localName) {
            item = event.target.parentElement.parentElement.parentElement;
          }

          // because of click on svg, we need to access to grandParent and not parent
          var drop_menus = document.getElementsByClassName("header__drop_menu");
          var drop_menu = item.getElementsByClassName("header__drop_menu")[0];

          Array.from(drop_menus).forEach(function(e) {
            if(e !== drop_menu){
              e.classList.remove("header__display");
            }
          });

          var lis = document.getElementById("headerMenu").getElementsByTagName("li");
          Array.from(lis).forEach((e) => {
            e.style.marginTop = 0;
          });

          (!drop_menu.classList.contains("header__display")) ? drop_menu.classList.add("header__display") : drop_menu.classList.remove("header__display");

          if(window.innerWidth < 660 && drop_menu.classList.contains("header__display")) {
            //item.nextSibling.nextSibling.style.marginTop = drop_menu.clientHeight + "px";
          }
        }

      },
      displaySearch: function(hide) {
        this.hide = !hide;
        if (true === this.hide) {
          this.$nextTick(() => {
            this.$refs.search.$children[0].$refs.input.focus();
          });
        }
      },
      isHomepage: function() {
        if(this.currentRoute === this.homeRoute) {
          this.isHome = true;
        } else {
          this.isHome = false
        }
      }
      /*openSlideMenu: function () {
        this.$refs.slideMenu.$children[0].openMenu();
      },*/
      /*switchTheme: function(theme) {
        this.theme = ("moon" !== theme) ? "moon" : "moon-empty";
        this.titleSwitchMode = ("moon" !== theme) ? titleNightMode : titleDayMode;
      }*/
    },
    beforeMount() {
      this.currentFlag = 'flag_' + this.currentLocale;
      this.isHomepage();
    }
  }

  function closeAllMenu() {
    var lis = document.getElementById("headerMenu").getElementsByTagName("li");
    Array.from(lis).forEach(function(e){
      e.style.marginTop = 0;
    });

    var drop_menus = document.getElementsByClassName("header__drop_menu");
    Array.from(drop_menus).forEach(function(e){
      e.classList.remove("header__display");
    });
  }
</script>
<!-- https://vuejsexamples.com/vue-js-header-responsive-dropdown-menu/ -->
