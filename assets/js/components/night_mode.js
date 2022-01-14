const CLASS_NIGHT_MODE = 'night';

/**
 * Store mode
 * @param value
 */
const store_mode = (value) => {
  if (CLASS_NIGHT_MODE === value) {
    localStorage.setItem('astro.otter.mode', value);
  } else {
    localStorage.clear();
  }
};


/**
 * check if value exist
 * @returns {string}
 */
const is_night_mode = () => {
  return localStorage.getItem('astro.otter.mode');
};

/**
 * Remove class night
 * @returns {null}
 */
const set_day_mode = () => {
  document.querySelectorAll('.' + CLASS_NIGHT_MODE).forEach(el => {
    el.classList.remove(CLASS_NIGHT_MODE)
  });
  store_mode(null);
  return null;
};

/**
 * Apply class 'nigh' on items
 * @returns {null}
 */
const set_night_mode = (listItems, listMultiItems) => {
  [
    'body',
    '.header__notHome',
    '.header__search',
    '.AppSearch__inputTextHome',
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
    }
  });

  [
    '.header__menu__notHome',
    '.breadcrumb a',
    'span.bm-burger-bars',
    '.header__drop_menu',
    '.header__search',
    '.AppSearch__list li a',
    '.header__menu li a',
    '.header__drop_menu a',
    '.Dso__title',
    '.Dso__list a',
    '.Dso__td',
    '.appGridFacet__item a',
    'article.card',
    '.Form__input',
    '.Form__select',
    '.Form__textarea',
    'td',
    'a'
  ].forEach(item => {
    if (null !== document.querySelectorAll(item) && 0 < document.querySelectorAll(item).length) {
      document.querySelectorAll(item).forEach(el => el.classList.add(CLASS_NIGHT_MODE));
    }
  });

  store_mode(CLASS_NIGHT_MODE);

  return null;
};

export default {
  setNightMode: set_night_mode(),
  setDayMode: set_day_mode(),
  isNightMode: is_night_mode()
}
