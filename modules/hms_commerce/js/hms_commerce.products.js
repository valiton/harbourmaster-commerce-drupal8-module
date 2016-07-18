/**
 * @file
 * Attaches hms_commerce behaviors to forms with products.
 */
(function($) {

  "use strict";

  Drupal.behaviors.hms_commerceProducts = {
    attach: function(context, settings) {

      // Configure widgets.
      var apiSource = settings.hms_commerce.api_source;
      window._digtapq = window._digtapq || [];
      window._digtapq.push(['configure', {
        api: apiSource
      }]);

      // Place widgets into product divs created by Drupal.
      var productIds = settings.hms_commerce.product_ids;
      var widgetType = settings.hms_commerce.widget_type;
      $.each(productIds, function(index, value) {
        window._digtapq.push(['render', {
          widget: widgetType,
          selector: '#digtap-widget-' + index,
          options: {
            product_id: Number(value)
          }
        }]);
      });
    }
  };
})(jQuery);
