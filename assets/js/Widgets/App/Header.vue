<template>
  <header class="header">

    <Slide :burgerIcon="false" ref="slideMenu" width="300">
      <a v-for="menu in leftSideMenu" v-bind:href="menu.path">
        <i v-bind:class="menu.icon_class"></i>
        <span>{{menu.label}}</span>
      </a>

      <hr />
      <a id="github" href="https://github.com/HamHamFonFon/deep-space-objects" title="Github" target="_blank">
        <i class="fab fa-github"></i>
        <span>Github</span>
      </a>
      <p>
        2019 - {{title}}
      </p>
    </Slide>

    <div class="header__wrap">
      <h1 class="h1 h1__title" >
        <span v-on:click="openSlideMenu" class="header__barSlideMenu" title="Open menu">
          <i class="fas fa-bars"></i>
        </span>&nbsp;
        <a v-bind:href="homepageRoute" v-bind:title="title">{{title}}</a>
      </h1>

      <nav id="headerMenu" class="header__menu">
        <!--Search-->
        <li v-if="currentRoute !== homeRoute">
          <a v-on:click="displaySearch(hide);">
            <i class="fas fa-search"></i>
          </a>
        </li>
        <!--Menu-->
        <li class="header__drop">
          <a v-on:click="displayDropMenu()">
            <i class="far fa-flag"></i>
          </a>
          <ul class="header__drop_menu">
            <a v-for="locale in listLocales" v-bind:href="locale.path" :key="locale.locale">
              {{ locale.label }}
            </a>
          </ul>
        </li>
        <!--Current locale-->
        <li class="header__currentLang">{{currentLocale.toUpperCase()}}</li>
      </nav>
    </div>

    <div class="header__search" v-if="hide">
      <searchautocomplete
        ref="search"
        :searchPlaceholder="searchPlaceholder"
        :customClasses="autoCompleteClasse"
        :url="searchUrl"
      ></searchautocomplete>
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

  import searchautocomplete from './../Homepage/components/Searchautocomplete';
  import { Slide } from 'vue-burger-menu'

  window.addEventListener("resize", function(event) {
    closeAllMenu();
    // document.getElementsByTagName("body")[0].classList.remove("display_menu");
  });

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

        currentRoute: routeSf,
        homeRoute: 'homepage',
        hide: false,
        autoCompleteClasse: {
          wrapper: 'AppHeader__wrapper',
          input: 'AppSearch__inputText',
          list: 'AppHeader__list'
        },
        searchPlaceholder: placeholder,
        searchUrl: urlSearch
      }
    },
    methods: {
      displayDropMenu: function() {
        var drop_menu = event.target.parentElement.parentElement.getElementsByClassName("header__drop_menu")[0];
        var drop_menus = document.getElementsByClassName("header__drop_menu");

        Array.from(drop_menus).forEach(function(e){
          if(e != drop_menu){
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
