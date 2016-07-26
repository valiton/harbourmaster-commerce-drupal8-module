/**
 * @file
 * Attaches hms_commerce behaviors to forms with products.
 */
(function($) {

  "use strict";

  Drupal.behaviors.hms_commerceProducts = {
    attach: function(context, settings) {

      // Configure widgets.
      var bestsellerUrl = settings.hms_commerce.bestseller_url;
      window._digtapq = window._digtapq || [];
      window._digtapq.push(['configure', {
        api: bestsellerUrl
      }]);

      // Place widgets into product divs created by Drupal.
      var productIds = settings.hms_commerce.product_ids;
      var widgetType = settings.hms_commerce.widget_type;
      $.each(productIds, function(index, value) {
        window._digtapq.push(['render', {
          widget: widgetType,
          selector: '#digtap-widget-' + widgetType + '-' + index,
          options: {
            product_id: Number(value)
          }
        }]);
      });
    }
  };
})(jQuery);
