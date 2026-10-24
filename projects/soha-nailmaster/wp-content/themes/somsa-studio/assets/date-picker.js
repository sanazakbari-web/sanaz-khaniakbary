(function (root) {
  'use strict';
  const formatter = new Intl.DateTimeFormat('fa-IR-u-ca-persian', {
    calendar: 'persian', numberingSystem: 'arabext', timeZone: 'Asia/Tehran',
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
  });
  function format(iso) {
    const parts = Object.fromEntries(formatter.formatToParts(new Date(iso + 'T12:00:00Z')).map(part => [part.type, part.value]));
    return `${parts.weekday}، ${parts.day} ${parts.month} ${parts.year}`;
  }
  function options(min, max) {
    const start = new Date(min + 'T12:00:00Z');
    const end = new Date(max + 'T12:00:00Z');
    const days = [];
    for (let day = start; day <= end; day = new Date(day.getTime() + 86400000)) {
      const value = day.toISOString().slice(0, 10);
      days.push({ value, label: format(value) });
    }
    return days;
  }
  function init(select) {
    if (!select) return;
    const previous = select.value;
    select.replaceChildren(new Option('تاریخ شمسی را انتخاب کنید', ''));
    options(select.dataset.min, select.dataset.max).forEach(day => select.add(new Option(day.label, day.value)));
    if ([...select.options].some(option => option.value === previous)) select.value = previous;
  }
  const api = { format, options, init };
  if (typeof module !== 'undefined' && module.exports) module.exports = api;
  else root.SomsaDates = api;
})(typeof window !== 'undefined' ? window : undefined);
