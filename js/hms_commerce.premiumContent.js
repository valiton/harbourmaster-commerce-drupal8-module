/**
 * @file
 * Triggers all Drupal js behaviours after the decryption of content.

 */
(function($) {

  "use strict";

  window.premiumContetReady =false;
  window.hasPremiumContet = true;
  Drupal.behaviors.hms_commercePremiumContent = {

    attach: function(context, settings) {

      // Triggers all Drupal js behaviours after the decryption of content.
      window.hmsAccess.setOnUpdateHandler(function() {
        window.premiumContetReady = true;
        Drupal.attachBehaviors(); //todo: Might want to limit this to encrypted DOM element.
      });
    }
  };
})(jQuery);
