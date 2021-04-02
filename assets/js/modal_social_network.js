import { default as fade } from "./components/fade";

/**
 *
 * @type {{init, setCookie}}
 */
;(function() {

  const CONTAINER_POPIN = 'Popup--news';

  const COOKIE_NAME = "popup_newsletter";

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
      checkCookie();
    }
  };


  /**
   * Check cookie and display/hide popin
   */
  let checkCookie = () => {
    const isCookie = getCookie();

    // If there is a cookie, we hide popup
    if ("1" === isCookie) {
      hidePopin();
    } else {
      // Get nb page view by user
      var nbPageUser = parseInt(localStorage.getItem("pageCount"));
      if (isNaN(nbPageUser)) {
        nbPageUser = 1;
      }
      // Get the number of page (1 or 2) when popin appear
      var showPopin = parseInt(document.getElementById(CONTAINER_POPIN).dataset.popupDisplaypage);

      if (nbPageUser >= showPopin) {
        displayPopin();
      } else {
        hidePopin();
      }
    }
  };

  let displayPopin = () => {
    setTimeout(function() {
      fade.fadeIn(CONTAINER_POPIN);
    }, 4000)
  };

  let hidePopin = () => {
    setTimeout(function() {
      fade.fadeOut(CONTAINER_POPIN)
    }, 4000)
  };

  /**
   *
   */
  const writePageCount = () => {
    let pageCount = localStorage.getItem('pageCount');
    if (null === pageCount) {
      localStorage.setItem('pageCount', 1);
    } else {
      pageCount = parseInt(pageCount) + 1;
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
      var date = new Date();
      date.setTime(date.getTime() + (days * 24 *60 * 60 * 1000));
      expires = "; expires=" + date.toUTCString();
    }
    document.cookie = COOKIE_NAME + "=1" + expires + "; path=/";
  };

  /**
   * Delete cookie
   */
  let eraseCookie = () => {
    document.cookie = COOKIE_NAME +'=; Max-Age=-99999999;';
  };

  /*document.addEventListener("click touchend", function () {
    if (0 !== document.getElementById(CONTAINER_POPIN).length) {
      // When user click on popup to close it, set cookie to "1"
      setCookie(365);
      hidePopin();
    }
  });

  document.addEventListener("DOMContentLoaded", () => {
    init();
  });*/
})();
