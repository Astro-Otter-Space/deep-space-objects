const CLASS_NIGHT_MODE = 'night';

const day_mode = () => {
  document.querySelectorAll('.' + CLASS_NIGHT_MODE).forEach(el => {
    el.classList.remove(CLASS_NIGHT_MODE)
  })
};

const night_mode = () => {
  set_mode(CLASS_NIGHT_MODE);

  // adding class to items
  // -- add class to header
  // -- add class to td
  // -- add class to main
};

const set_mode = (value) => {
  if (CLASS_NIGHT_MODE === value) {
    localStorage.setItem('astro.otter.mode', value);
  } else {
    localStorage.clear();
  }
};

export default {
  nightMode: night_mode,
  dayMode: day_mode
}
