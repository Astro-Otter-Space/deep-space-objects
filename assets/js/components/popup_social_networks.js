import {fadeIn, fadeOut} from "./fade";

const CONTAINER_POPIN = 'popup1';
const COOKIE_NAME = "astro-otter-popup";
const el = document.getElementById(CONTAINER_POPIN);

/**
 * Init checking cookie
 */
let init = () => {
  displayPopin(); /*
  writePageCount();
  if (
    false === !!document.getElementById(CONTAINER_POPIN)
    || "disabled" === document.getElementById(CONTAINER_POPIN).dataset.popupState
  ) {
    // remove cookie
    eraseCookie(COOKIE_NAME);
  } else {
    checkCookie();
  }*/
};

/**
 * @TODO CHANGE
 * Check cookie and display/hide popin
 */
let checkCookie = () => {
  const isCookie = getCookie();

  // If there is a cookie, we hide popup
  if ("1" === isCookie) {
    hidePopin();
  } else {
    // Get nb page view by user
    let nbPageUser = parseInt(localStorage.getItem("pageCount"));
    if (isNaN(nbPageUser)) {
      nbPageUser = 1;
    }
    // Get the number of page (1 or 2) when popin appear
    const showPopin = parseInt(document.getElementById(CONTAINER_POPIN).dataset.popupDisplaypage);

    if (nbPageUser >= showPopin) {
      displayPopin();
    } else {
      hidePopin();
    }
  }
};

let displayPopin = () => {
  fadeIn(el, 2000);
};

let hidePopin = () => {
  fadeOut(el, 4000);
};

/**
 *
 */
const writePageCount = () => {
  let pageCount = parseInt(localStorage.getItem('pageCount'));
  if (null === pageCount || true === Number.isNaN(pageCount)) {
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
  if (0 !== el.length) {
    // When user click on popup to close it, set cookie to "1"
    setCookie(30);
    hidePopin();
  }
};

export default {
  init: init,
  hidePopin: hidePopin,
  setCookieAndClosePopin: setCookieAndClosePopin
};
