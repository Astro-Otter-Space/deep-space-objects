import flatpickr from "flatpickr";

export function datePicker() {
  return flatpickr('input.js-datepicker', {
    enableTime: true,
  });
};
