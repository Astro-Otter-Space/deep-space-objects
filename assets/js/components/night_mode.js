const CLASS_NIGHT_MODE = 'night';

const store_mode = (value) => {
  if (CLASS_NIGHT_MODE === value) {
    localStorage.setItem('astro.otter.mode', value);
  } else {
    localStorage.clear();
  }
};

const day_mode = () => {
  document.querySelectorAll('.' + CLASS_NIGHT_MODE).forEach(el => {
    el.classList.remove(CLASS_NIGHT_MODE)
  });
  store_mode(null);
};

const night_mode = () => {
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
    '.header__menu li a',
    '.header__drop_menu a',
    '.Dso__title',
    'Dso__list a',
    'Dso__td',
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
};

export default {
  nightMode: night_mode,
  dayMode: day_mode
}
