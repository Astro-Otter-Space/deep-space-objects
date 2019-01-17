<template>
  <header class="header">
    <div class="header__wrap">
      <h1 class="h1 h1__title">
        <i class="fas fa-bars"></i>&nbsp;<a href="/">{{title}}</a>
      </h1>
      <nav id="headerMenu" class="header__menu">
        <!--Search-->
        <li v-if="currentRoute !== homeRoute">
          <a v-on:click="hide = !hide">
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
        searchPlaceholder="Test..."
      ></searchautocomplete>
    </div>
  </header>
</template>

<script>
  let routeSf = document.getElementById('appHeader').dataset.route;
  let listLocales = JSON.parse(document.getElementById('appHeader').dataset.locales);
  let currentLocale = document.getElementById('appHeader').dataset.currentlocale;
  let title = document.getElementById('appHeader').dataset.title;

  import searchautocomplete from './../Homepage/components/Searchautocomplete';

  window.addEventListener("resize", function(event) {
    closeAllMenu();
    // document.getElementsByTagName("body")[0].classList.remove("display_menu");
  });

  export default {
    name: "Header",
    components: {
      searchautocomplete
    },
    data() {
      return {
        title: title,
        currentLocale: currentLocale,
        listLocales: listLocales,
        currentRoute: routeSf,
        homeRoute: 'homepage',
        hide: false
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
