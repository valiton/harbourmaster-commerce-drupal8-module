/**
 * @file
 * Attaches hms_commerce behaviors to the digtap_product field widget.
 */
(function($) {

  "use strict";

  Drupal.behaviors.hms_commerceDigtapProductWidget = {
    attach: function(context, settings) {

      // Configure widgets.
      var bestsellerUrl = settings.hms_commerce.bestseller_url;
      //window._digtapq = window._digtapq || [];
      //window._digtapq.push(['configure', {
      //  api: bestsellerUrl
      //}]);

      $(".digtap-product-widget").click(function() {
        alert(this.id);
      });
    }
  };
})(jQuery);
