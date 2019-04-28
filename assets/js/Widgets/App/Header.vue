<template>
  <header class="header">

    <Slide :burgerIcon="false" ref="slideMenu" width="300">
      <!--TODO : target blank-->
      <a v-for="menu in leftSideMenu" v-bind:href="menu.path" v-bind:title="menu.label">
        <svgicon v-bind:name="menu.icon_class" width="30" height="30" color="#e9e9e9"></svgicon>
        <span>{{menu.label}}</span>
      </a>
    </Slide>

    <div class="header__wrap">
      <h1 class="h1 h1__title" >
<!--        Open Menu-->
        <span v-on:click="openSlideMenu" class="header__barSlideMenu" v-bind:title="titleOpenMenu">
          <svgicon name="bars" width="30" height="30"></svgicon>
        </span>&nbsp;
        <a v-bind:href="homepageRoute" v-bind:title="title">{{title}}</a>
      </h1>

      <nav id="headerMenu" class="header__menu">
        <!--Search-->
        <li v-if="currentRoute !== homeRoute">
          <a v-on:click="displaySearch(hide);" v-bind:title="searchPlaceholder">
            <svgicon name="search" width="30" height="30" color="#e9e9e9"></svgicon>
          </a>
        </li>

        <!-- Dark/day mod -->
        <li>
          <a v-on:click="switchTheme(theme)" v-bind:title="titleSwitchMode">
            <svgicon v-bind:name="theme" width="30" height="30" color="#e9e9e9"></svgicon>
          </a>
        </li>

        <!--Languages-->
        <li class="header__drop">
          <a v-on:click="displayDropMenu()" v-bind:title="titleSwitchLang">
            <svgicon name="globe" width="30" height="30" color="#e9e9e9"></svgicon>
          </a>
          <ul class="header__drop_menu">
            <a v-for="locale in listLocales" v-bind:href="locale.path" :key="locale.locale" v-bind:title="locale.label">
              {{ locale.label }}
            </a>
          </ul>
        </li>

        <!--Current locale-->
        <li class="header__currentLang">{{currentLocale.toUpperCase()}}</li>
      </nav>
    </div>

    <div class="header__search" v-if="hide">
      <transition>
        <searchautocomplete
          ref="search"
          :searchPlaceholder="searchPlaceholder"
          :customClasses="autoCompleteClasse"
          :url="searchUrl"
        ></searchautocomplete>
      </transition>
    </div>
  </header>
</template>

<script>
  let homeRoute = document.getElementById('appHeader').dataset.homeRoute;
  let leftSideMenu = JSON.parse(document.getElementById('appHeader').dataset.menuSide);
  let routeSf = document.getElementById('appHeader').dataset.route;
  let listLocales = JSON.parse(document.getElementById('appHeader').dataset.locales);
  let currentLocale = document.getElementById('appHeader').dataset.currentlocale;
  let title = document.getElementById('appHeader').dataset.title;
  let placeholder = document.getElementById('appHeader').dataset.searchPlaceholder;
  let urlSearch = document.getElementById('appHeader').dataset.searchRoute;

  let titleOpenMenu = "Open menu";
  let titleSwitchLang = "Switch language";
  let titleNightMode = "Switch to night mode";
  let titleDayMode = "Switch to day mode";

  import searchautocomplete from './../Homepage/components/Searchautocomplete';
  import { Slide } from 'vue-burger-menu';
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
      searchautocomplete,
      Slide
    },
    data() {
      return {
        homepageRoute: homeRoute,
        leftSideMenu: leftSideMenu,
        title: title,
        listLocales: listLocales,
        currentLocale: currentLocale,
        theme: themeLocalStorage.fetch(),
        currentRoute: routeSf,
        homeRoute: 'homepage',
        hide: false,
        autoCompleteClasse: {
          wrapper: 'AppHeader__wrapper',
          input: 'AppSearch__inputText',
          list: 'AppHeader__list'
        },
        searchPlaceholder: placeholder,
        searchUrl: urlSearch,
        titleOpenMenu: titleOpenMenu,
        titleSwitchLang: titleSwitchLang,
        titleSwitchMode: titleNightMode
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
      displayDropMenu: function() {
        var drop_menu = event.currentTarget.parentElement.parentElement.getElementsByClassName("header__drop_menu")[0];
        var drop_menus = document.getElementsByClassName("header__drop_menu");

        Array.from(drop_menus).forEach(function(e){
          if(e !== drop_menu){
            e.classList.remove("header__display");
          }
        });

        var lis = document.getElementById("headerMenu").getElementsByTagName("li");
        Array.from(lis).forEach(function(e){
          e.style.marginTop = 0;
        });

        (!drop_menu.classList.contains("header__display")) ? drop_menu.classList.add("header__display") : drop_menu.classList.remove("header__display");


        if(window.innerWidth < 660 && drop_menu.classList.contains("header__display")) {
          event.target.parentElement.nextSibling.nextSibling.style.marginTop = drop_menu.clientHeight + "px";
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
      openSlideMenu: function () {
        this.$refs.slideMenu.$children[0].openMenu();
      },
      switchTheme: function(theme) {
        this.theme = ("moon" !== theme) ? "moon" : "moon-empty";
        this.titleSwitchMode = ("moon" !== theme) ? titleNightMode : titleDayMode;
      }
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
<!--https://vuejsexamples.com/vue-js-header-responsive-dropdown-menu/-->
