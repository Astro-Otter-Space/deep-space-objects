<template>
  <header v-bind:class="[ !this.isHome  ? 'header__notHome': '', 'header']" data-dark-mode>

    <Slide :burgerIcon="false" disableOutsideClick ref="slideMenu" >
      <a v-for="menu in leftSideMenu" v-bind:href="menu.path" v-bind:title="menu.label">
        <svgicon v-bind:name="menu.icon_class" width="36" height="36" color="#e9e9e9"></svgicon>
        <span>{{menu.label}}</span>
      </a>
<!--      <ul v-if="menu.subMenu && 0 < menu.subMenu.length" class="header__drop_submenu">-->
<!--        <li v-for="sub in menu.subMenu" class="header__drop_submenu_li">-->
<!--          <a v-bind:href="sub.path" v-bind:title="sub.label" class="header__drop_submenu_a">-->
<!--            <span>{{ sub.label }}</span>-->
<!--          </a>-->
<!--        </li>-->
<!--      </ul>-->
      <!-- dark mode -->
      <dark-mode></dark-mode>
    </Slide>

    <div class="header__wrap">
      <!-- Open Menu-->
     <nav v-on:click="openSlideMenu()" v-bind:title="titleOpenMenu" data-menu="header" v-bind:class="[ !this.isHome  ? 'header__menu__notHome': '', 'header__menu']">
       <svgicon name="bars" width="40" height="40" color="#e9e9e9"></svgicon>
     </nav>

      <nav data-menu="header" v-bind:class="[ !this.isHome  ? 'header__menu__notHome': '', 'header__menu']" data-dark-mode>
        <span class="h1 h1__title">
          <a v-bind:href="homepageRoute" v-bind:title="title">
            <span itemprop="name">{{title}}</span>
          </a>
        </span>
      </nav>

      <nav data-menu="header" v-bind:class="[ !this.isHome  ? 'header__menu__notHome': '', 'header__menu']" data-dark-mode>
        <ul class="header__wrap__sup_ul">
          <li v-if="!this.isHome">
            <a v-on:click="displaySearchHeader(hide)" v-bind:title="placeholder">
              <svgicon name="search" width="40" height="40" color="#e9e9e9"></svgicon>
            </a>
          </li>

          <!--Languages-->
          <li class="header__drop">
            <a v-on:click="displayDropMenu" v-bind:title="titleSwitchLang">
              <svgicon name="globe" width="40" height="40" color="#e9e9e9"></svgicon>
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
        </ul>
      </nav>
    </div>

    <div class="header__search" v-show="hide">
      <transition>
        <searchautocomplete
          ref="search"
          :searchPlaceholder="placeholder"
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
  let leftSideMenu = JSON.parse(document.getElementById('appHeader').dataset.menuMobile);
  let notification = JSON.parse(document.getElementById('appHeader').dataset.notification);
  let constellation = JSON.parse(document.getElementById('appHeader').dataset.constellation);
  let routeSf = document.getElementById('appHeader').dataset.route;
  let listLocales = JSON.parse(document.getElementById('appHeader').dataset.locales);
  let currentLocale = document.getElementById('appHeader').dataset.currentlocale;
  let urlSearch = document.getElementById('appHeader').dataset.searchRoute;
  let labelsTrans = JSON.parse(document.getElementById('appHeader').dataset.labels);

  import searchautocomplete from './../Homepage/components/Searchautocomplete';
  import { Slide } from 'vue-burger-menu';
  import './../Icons/index';
  import deviceDetect from 'mobile-device-detect';
  import darkMode from './darkMode';

  window.addEventListener("resize", function(event) {
    closeAllMenu();
  });

  export default {
    name: "Header",
    components: {
      searchautocomplete,
      Slide,
      darkMode
    },
    data() {
      return {
        homepageRoute: homeRoute,
        leftSideMenu: leftSideMenu,
        notification: notification,
        constellation: constellation,
        isNewData: false,
        title: labelsTrans.title,
        listLocales: listLocales,
        currentLocale: currentLocale,
        currentFlag: 'flag_en',
        currentRoute: routeSf,
        homeRoute: 'homepage',
        hide: false,
        autoCompleteClasse: {
          wrapper: 'AppHeader__wrapper',
          input: (deviceDetect.isMobileOnly) ? 'AppSearch__inputText AppSearch__inputTextHome' : 'AppSearch__inputText',
          list: 'AppHeader__list'
        },
        placeholder: (deviceDetect.isMobileOnly) ? labelsTrans.searchPlaceholderMobile : labelsTrans.searchPlaceholder,
        searchUrl: urlSearch,
        titleOpenMenu: labelsTrans.openMenu,
        titleSwitchLang: labelsTrans.switchLang,
        // titleSwitchMode: labelsTrans.nightMode,
        titleData: labelsTrans.titleData,
        isMobile: deviceDetect.isMobileOnly,
        darkMode: false
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
          const drop_menus = document.getElementsByClassName("header__drop_menu");
          const drop_menu = item.getElementsByClassName("header__drop_menu")[0];

          Array.from(drop_menus).forEach(function(e) {
            if(e !== drop_menu){
              e.classList.remove("header__display");
            }
          });

          let navHeaders = document.querySelectorAll('[data-menu="header"]');
          navHeaders.forEach(navHeader => {
            const lis = navHeader.getElementsByTagName("li");
            Array.from(lis).forEach(e => {
              e.style.marginTop = 0;
            });
          });

          (!drop_menu.classList.contains("header__display")) ? drop_menu.classList.add("header__display") : drop_menu.classList.remove("header__display");

          if(window.innerWidth < 660 && drop_menu.classList.contains("header__display")) {
            //item.nextSibling.nextSibling.style.marginTop = drop_menu.clientHeight + "px";
          }
        }
      },
      displaySearchHeader: function(hide) {
        this.hide = !hide;
        if (true === this.hide) {
          this.$nextTick(() => {
            this.$refs.search.$children[0].$refs.input.focus();
          });
        }
      },
      isHomepage: function() {
        this.isHome = this.currentRoute === this.homeRoute;
      },
      openSlideMenu: function() {
        this.$refs.slideMenu.$children[0].openMenu();
      },
      themeToggle: function() {
        if(this.darkMode || document.querySelector('body').classList.contains('night')) {
          console.log('Remove dark mode');
        } else {
          console.log('Add dark mode');
        }
      }
    },
    beforeMount() {
      this.currentFlag = 'flag_' + this.currentLocale;
      this.isHomepage();
    }
  }

  const closeAllMenu = () => {
    let navHeaders = document.querySelectorAll('[data-menu="header"]');
    navHeaders.forEach(navHeader => {
      const lis = navHeader.getElementsByTagName("li");
      Array.from(lis).forEach(e => {
        e.style.marginTop = 0;
      });
    });

    const drop_menus = document.getElementsByClassName("header__drop_menu");
    Array.from(drop_menus).forEach(function(e){
      e.classList.remove("header__display");
    });
  }
</script>
<!-- https://vuejsexamples.com/vue-js-header-responsive-dropdown-menu/ -->
