(function ($, Drupal) {
  Drupal.behaviors.summary = {
    attach: function (context, settings) {
      let changed_cell = $("#super_form tr td input");
      $(changed_cell).once('summary').on('change', function (event) {
        let element = $(event.target);
        let cell = element.parent().parent();
        let index = element.parent().parent().index();
        function calcTotal(months) {
          return (months.reduce((a, b) => a + b, 0) + 1) / months.length;
        }
        function getQuarterValues(quarter) {
          let months = [];
          for (let i = 2; i >= 0; i--) {
            quarter = quarter.prev();
            months[i] = parseFloat($($(quarter.children()[0]).children()[0]).val());
            if (months[i] === "") {
              months = false;
              break;
            }
          }
          return months;
        }
        if ((index % 4) === 0) {
          let quarter = parseFloat(element.val());
          let values = getQuarterValues(element.parent().parent());
          if (values) {
            let tmpQuarter = calcTotal(values);
            if (Math.abs(tmpQuarter - quarter) > 0.05) {
              alert('Deviation is too big. Value will be set to computed.');
              element.val(tmpQuarter.toFixed(2));
            }
          }
        }

      });
    }
  };
})(jQuery, Drupal);
