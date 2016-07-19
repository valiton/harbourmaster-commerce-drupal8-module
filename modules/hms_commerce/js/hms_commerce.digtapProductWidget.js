/**
 * @file
 * Attaches hms_commerce behaviors to the digtap_product field widget.
 */
(function($) {

  "use strict";

  Drupal.behaviors.hms_commerceDigtapProductWidget = {
    attach: function(context, settings) {

      // Configure widgets.
      var apiSource = settings.hms_commerce.api_source;
      //window._digtapq = window._digtapq || [];
      //window._digtapq.push(['configure', {
      //  api: apiSource
      //}]);

      $(".digtap-product-widget").click(function() {
        alert(this.id);
      });
    }
  };
})(jQuery);
