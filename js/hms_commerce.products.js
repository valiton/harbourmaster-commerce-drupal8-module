/**
 * @file
 * Makes the digtap_product and premium_content field formatters display digtap
 * widgets.
 */
(function($) {

  "use strict";

  Drupal.behaviors.hms_commerceProducts = {
    attach: function(context, settings) {

      // Globally configure digtap widgets.
      var bestsellerUrl = settings.hms_commerce.bestseller_url;
      window._digtapq = window._digtapq || [];
      window._digtapq.push(['configure', {
        api: bestsellerUrl,
        newsletter: settings.hms_commerce.newsletter
      }]);

      // Gather settings applying to all Digtap field formatters on the page.
      var formatterSettings = settings.hms_commerce.formatter_settings;

      // Looping through all field formatter types on the page.
      $.each(formatterSettings, function(widgetType, widgetSettings) {

        // Looping through all field formatters of this type.
        $.each(widgetSettings, function(fieldName, fieldSettings) {

          var productIds = fieldSettings.product_ids;
          var fieldDomId = fieldSettings.field_dom_id;

          // Looping through all products within this formatter.
          $.each(productIds, function(productIdIndex, productId) {

            var options = { product_id: Number(productId) };
            if (fieldSettings.premium_content_url) {
              options['content_url'] = fieldSettings.premium_content_url;
            }

            // Configure digtap widget for each product.
            window._digtapq.push(['render', {
              widget: widgetType,
              selector: '#' + fieldDomId + '-' + productIdIndex,
              options: options
            }]);

          });
        });
      });
    }
  };
})(jQuery);
