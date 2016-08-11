/**
 * @file
 * Attaches hms_commerce behaviors to the digtap_product drupal field widget.
 */
(function($) {

  "use strict";

  Drupal.behaviors.hms_commerceDigtapProductWidget = {
    attach: function(context, settings) {

      // Configure digtap widgets.
      var bestsellerUrl = settings.hms_commerce.bestseller_url;
      window._digtapq = window._digtapq || [];
      window._digtapq.push(['configure', {
        api: bestsellerUrl
      }]);

      // Gather settings applying to all field widgets on the page.
      var widgetSettings = settings.hms_commerce.digtap_product_widget_settings;

      $.each(widgetSettings, function(index, value) {
        window._digtapq.push(['init', {
          inputSelector: '#' + value.input_id,
          selector: '#' + value.container_id
        }]);
      });
    }
  };
})(jQuery);
