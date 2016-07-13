/**
 * @file
 * Attaches hms_commerce behaviors to forms with products.
 */
(function($) {

  "use strict";

  Drupal.behaviors.hms_commerceProducts = {
    attach: function(context, settings) {
      var apiSource = settings.hms_commerce.api_source;
      window._digtapq = window._digtapq || [];
      window._digtapq.push(['configure', {
        api: apiSource
      }]);

      //window._digtapq.push(['render', {
      //  widget: 'PremiumDownload',
      //  selector: '#digtap-widget',
      //  options: {
      //    product_id: 1
      //  }
      //}]);
    }
  };
})(jQuery);
