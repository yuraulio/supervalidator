(function ($, Drupal) {
  Drupal.behaviors.summary = {
    attach: function (context, settings) {
      $("#super_form tr td input").once('summary').on('change', function (event) {
        let el = $(event.target);
        let elValue = parseFloat(el.val());
        let index = el.parent().parent().index();
        let values = [];
          function calcTotal(months) {
          return (months.reduce((a, b) => a + b, 0) + 1) / months.length;
        }
        function getValues(quarter) {
          let values = [];
          let current = el.parent().parent();
          let q = 3;
          if (typeof quarter != 'undefined') {
            current = quarter;
            q = 2;
          }
          for (let i = q; i >= 0; i--) {
            if (typeof quarter === 'undefined') {
              let n = 16 - 4 * (i);
              current = $("#super_form tr td").eq(n);
            } else {
              current = current.prev();
            }
            values[i] = parseFloat($($(current.children()[0]).children()[0]).val());
            if (isNaN(values[i])) {
              values = false;
              break;
            }
          }
          return values;
        }
        // Якщо немає порожніх місяців цього кварталу,
        // 1) обраховуємо квартал за формулою;
        // 2) порівнюємо це значення із введеним і
        // якщо воно не порожнє і виходить за межі
        // показуємо повідомлення і виправляємо значення;
        // 3) запускаємо подію зміни підсумку року.
        if ((index % 4) === 0) {
          values = getValues(el.parent().parent());
          if (values) {
            let tmpQuarter = calcTotal(values);
            if (Math.abs(tmpQuarter - elValue) > 0.05) {
              alert('Deviation is too big. Value will be set to computed.');
              // Додати перевірку на порожнє значення.
              el.val(tmpQuarter.toFixed(2));
            }
          }
          // Якщо немає порожніх квартальних значень,
          // 1) обраховуємо рік за формулою;
          // 2) порівнюємо це значення із введеним і
          // якщо воно не порожнє і виходить за межі,
          // показуємо повідомлення і виправляємо значення;
        } else if (index === 17) {
          values = getValues();
          console.log(values);
          if (values) {
            let tmpYear = calcTotal(values);
            if (Math.abs(tmpYear - elValue) > 0.05) {
              alert('Deviation is more then 0.05. Value will be set to computed.');
              // Додати перевірку на порожнє значення.
              el.val(tmpYear.toFixed(2));
            }
          }
          // Якщо немає порожніх місяців відповідного кварталу,
          // 1) запускаємо подію зміни кварталу.
        } else {
          let quarter = $("#super_form tr td").eq(((index / 4 >> 0) + 1) * 4);
          values = getValues(quarter);
          if (values) {
            let quarterTotal = calcTotal(values);
            console.log(quarter);
            $($(quarter.children()[0]).children()[0]).val(quarterTotal.toFixed(2));
            $($(quarter.children()[0]).children()[0]).triggerHandler('change');
          }
        }
      });
    }
  };
})(jQuery, Drupal);
