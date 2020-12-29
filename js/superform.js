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
        if ((index % 4) === 0) {
          values = getValues(el.parent().parent());
          console.log(values);
          if (values) {
            let tmpQuarter = calcTotal(values);
            if (Math.abs(tmpQuarter - elValue) > 0.05) {
              alert('Deviation is too big. Value will be set to computed.');
              el.val(tmpQuarter.toFixed(2));
            }
          }
        } else if (index === 17) {
          values = getValues();
          console.log(values);
          if (values) {
            let tmpYear = calcTotal(values);
            if (Math.abs(tmpYear - elValue) > 0.05) {
              alert('Deviation is more then 0.05. Value will be set to computed.');
              el.val(tmpYear.toFixed(2));
            }
          }
        }

      });
    }
  };
})(jQuery, Drupal);
