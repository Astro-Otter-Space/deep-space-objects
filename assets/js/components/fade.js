/**
 * FadeIn function
 * @param el
 * @param duration
 */
export const fadeIn = (el, duration) => {
  (function increment(value = 0) {
    el.style.opacity = String(value);
    if (el.style.opacity !== '1') {
      setTimeout(() => {
        increment(value + 0.1);
      }, duration / 10);
    }
  })();
};

/**
 * FadeOut function
 * @param el
 * @param duration
 */
export const fadeOut = (el, duration) => {
  (function decrement() {
    (el.style.opacity -= 0.1) < 0 ? el.style.display = 'none' : setTimeout(() => {
      decrement();
    }, duration / 10);
  })();
};
