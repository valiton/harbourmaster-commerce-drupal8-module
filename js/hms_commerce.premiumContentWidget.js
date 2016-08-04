/**
 * @file
 * Makes the premium_content field widget dynamic.
 */
(function($) {

  "use strict";

  Drupal.behaviors.hms_commercePremiumContentWidget = {
    attach: function(context, settings) {
      var fieldIds = settings.hms_commerce.premium_content_field_ids;

      $.each(fieldIds, function(index, value) {
        var checkbox = '#' + value + ' .form-checkbox';
        var selectList = '#' + value + ' .form-type-select';
        toggleListVisibility(checkbox, selectList);

        $(checkbox).change(function () {
          toggleListVisibility(checkbox, selectList);
        });
      });

      function toggleListVisibility(checkbox, selectList) {
        if ($(checkbox).is(":checked")) {
          $(selectList).show();
        } else {
          $(selectList + ' .form-select').val('');
          $(selectList).hide();
        }
      }
    }
  };
})(jQuery);
