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
      $.each(productIds, function(index, value) {
        window._digtapq.push(['render', {
          widget: 'PremiumDownload',
          selector: '#digtap-widget-' + value,
          options: {
            product_id: value
          }
        }]);
      });
    }
  };
})(jQuery);
