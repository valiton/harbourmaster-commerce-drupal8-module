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
      window._digtapq = window._digtapq || [];
      window._digtapq.push(['configure', {
        bestseller: {
          api: settings.hms_commerce.bestseller_url,
          client: settings.hms_commerce.bestseller_client
        },
        newsletter: settings.hms_commerce.newsletter,
        usermanager: {
          api: settings.hms_commerce.usermanager_url
        }
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

            var renderProperties = {
              widget: widgetType,
              selector: '#' + fieldDomId + '-' + productIdIndex,
              options: {
                product_id: Number(productId)
              }
            };

            // These settings are only available with the PremiumContent widget.
            if (widgetType == 'PremiumContent') {
              renderProperties.options['content_url'] = fieldSettings.premium_content.url;
              renderProperties.options['hms_external_id'] = fieldSettings.premium_content.id;

              renderProperties.onPurchaseFinished = function() {

                if(Drupal.behaviors.burdaInfinite){
                  console.log("we reload the page, workaround for infinite theme");
                  document.location.reload();
                } else {
                  window.hmsAccess && window.hmsAccess.update();
                }

              }
            }

            // Configure digtap widget for each product.
            window._digtapq.push(['render', renderProperties]);

          });
        });
      });
    }
  };
})(jQuery);
