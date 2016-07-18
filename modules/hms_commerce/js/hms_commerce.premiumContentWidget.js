/**
 * @file
 * Attaches hms_commerce behaviors to the premium_content field's form widget.
 */
(function($) {

  "use strict";

  Drupal.behaviors.hms_commercePremiumContentWidget = {
    attach: function(context, settings) {
      var checkbox = '.field--widget-premium-content .form-checkbox';
      toggleListVisibility();

      // On checkbox change, hide and show submit button
      $(checkbox).change(function () {
        toggleListVisibility();
      });

      function toggleListVisibility() {
        var selectList = '.field--widget-premium-content .form-type-select';
        if ($(checkbox).is(":checked")) {
          // show submit
          $(selectList).show();
        } else {
          $(selectList + ' .form-select').val('');
          $(selectList).hide();
        }
      }
    }
  };
})(jQuery);
