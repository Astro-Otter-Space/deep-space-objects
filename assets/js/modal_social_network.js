import { default as fade } from "./components/fade";

/**
 *
 * @type {{init, setCookie}}
 */
;(function() {

  const CONTAINER_POPIN = 'popup1';

  const COOKIE_NAME = "popup_social";

  /**
   * Init checking cookie
   */
  let init = () => {
    writePageCount();
    if (
        0 === document.getElementById(CONTAINER_POPIN).length
      || "disabled" === document.getElementById(CONTAINER_POPIN).dataset.popupState
    ) {
      // remove cookie
      eraseCookie(COOKIE_NAME);
    } else {
      document.querySelector('[data-popup-close]').addEventListener('click', setCookieAndClosePopin);
      checkCookie();
    }
  };

  /**
   * Check cookie and display/hide popin
   */
  let checkCookie = () => {
    const isCookie = 0; //getCookie();
    const el = document.getElementById(CONTAINER_POPIN);

    // If there is a cookie, we hide popup
    if ("1" === isCookie) {
      hidePopin(el);
    } else {
      // Get nb page view by user
      let nbPageUser = parseInt(localStorage.getItem("pageCount"));
      if (isNaN(nbPageUser)) {
        nbPageUser = 1;
      }
      // Get the number of page (1 or 2) when popin appear
      const showPopin = parseInt(document.getElementById(CONTAINER_POPIN).dataset.popupDisplaypage);

      if (nbPageUser >= showPopin) {
        displayPopin(el);
      } else {
        hidePopin(el);
      }
    }
  };

  let displayPopin = (el) => {
    setTimeout(function() {
      fade.fadeIn(el);
    }, 2000)
  };

  let hidePopin = (el) => {
    setTimeout(function() {
      fade.fadeOut(el)
    }, 4000)
  };

  /**
   *
   */
  const writePageCount = () => {
    let pageCount = parseInt(localStorage.getItem('pageCount'));
    if (null === pageCount) {
      localStorage.setItem('pageCount', 1);
    } else {
      pageCount = pageCount + 1;
      localStorage.setItem('pageCount', pageCount);
    }
  };

  /**
   * Retrieve cookie by name
   * @returns {string|number}
   */
  const getCookie = () => {
    let cookieValue = 0;
    let cookies;
    let i = 0;
    if (document.cookie !== "") {
      cookies = document.cookie.split("; ");
      for (i = 0; i < cookies.length; i++) {
        if (cookies[i].split('=')[0] === COOKIE_NAME) {
          return cookies[i].split('=')[1];
        }
      }
    }
    return cookieValue;
  };

  /**
   *
   * @param days
   */
  const setCookie = function(days) {
    let expires = "";
    if (days) {
      /** @var Date */
      const date = new Date();
      date.setTime(date.getTime() + (days * 24 *60 * 60 * 1000));
      expires = "; expires=" + date.toUTCString();
    }
    document.cookie = COOKIE_NAME + "=1" + expires + "; path=/";
  };

  /**
   * Delete cookie
   */
  const eraseCookie = () => {
    document.cookie = COOKIE_NAME +'=; Max-Age=-99999999;';
  };

  const setCookieAndClosePopin = () => {
    var el = document.getElementById(CONTAINER_POPIN);
    if (0 !== el.length) {
      // When user click on popup to close it, set cookie to "1"
      setCookie(30);
      hidePopin(el);
    }
  };

  document.addEventListener("touchend", setCookieAndClosePopin);
  document.addEventListener("click", setCookieAndClosePopin);
  // document.querySelector('button[data-popup-close]').addEventListener("click", setCookieAndClosePopin);

  document.addEventListener("DOMContentLoaded", () => {
    init();
  });
})();
