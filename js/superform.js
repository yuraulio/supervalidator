(function ($, Drupal) {
  Drupal.behaviors.summary = {
    attach: function (context, settings) {
      $("#super_form tr td input").once('summary').on('change', function (event) {
        let el = $(event.target);
        let cell = el.parent().parent();
        let elValue = parseFloat(el.val());
        let index = cell.index();
        function calcTotal(months) {
          return (months.reduce((a, b) => a + b, 0) + 1) / months.length;
        }
        function getValues() {
          let values = [];
          let current = cell;
          let q = 3;
          if (index < 17) {
            q = 2;
          }
          for (let i = q; i >= 0; i--) {
            if (index === 17) {
              let n = 16 - 4 * (i);
              current = $(cell.siblings()[n]);
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
        function checkAndSet(values) {
          if (values) {
            let tmpTotal = calcTotal(values);
            if (elValue) {
              if (Math.abs(tmpTotal - elValue) > 0.05) {
                alert('Deviation is too big. Value will be set to computed.');
                el.val(tmpTotal.toFixed(2));
              }
            } else {
              el.val(tmpTotal.toFixed(2));
            }
          }
        }
        if ((index % 4) === 0) {
          checkAndSet(getValues());
          $($($(cell.siblings()[16]).children()[0]).children()[0]).triggerHandler('change');
        } else if (index === 17) {
          checkAndSet(getValues());
        } else {
          let quarter = $(cell.siblings()[(((index / 4 >> 0) + 1) * 4) - 1]);
          $($(quarter.children()[0]).children()[0]).triggerHandler('change');
        }
      });
    }
  };
})(jQuery, Drupal);
