/**
 * @file
 * Makes the digtap_product field formatter display digtap widgets.
 */
(function($) {

  "use strict";

  Drupal.behaviors.hms_commerceProducts = {
    attach: function(context, settings) {

      // Configure digtap widgets.
      var bestsellerUrl = settings.hms_commerce.bestseller_url;
      window._digtapq = window._digtapq || [];
      window._digtapq.push(['configure', {
        api: bestsellerUrl
      }]);

      // Gather settings applying to all field formatters on the page.
      var widgetType = settings.hms_commerce.widget_type;
      var formatterSettings = settings.hms_commerce.digtap_product_formatter_settings;

      $.each(formatterSettings, function(index, value) {
        // Gather settings applying to this specific formatter.
        var productIds = value.product_ids;
        var fieldDomId = value.field_dom_id;

        // Configure digtap widget for each product within this formatter.
        $.each(productIds, function(index, value) {
          window._digtapq.push(['render', {
            widget: widgetType,
            selector: '#' + fieldDomId + '-' + index,
            options: {
              product_id: Number(value)
            }
          }]);
        });
      });
    }
  };
})(jQuery);
