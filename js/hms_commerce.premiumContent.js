/**
 * @file
 * Triggers all Drupal js behaviours after the decryption of content.
 */
(function($) {

  "use strict";

  Drupal.behaviors.hms_commercePremiumContent = {
    attach: function(context, settings) {
      window.hmsAccess.setOnUpdateHandler(function() {
            Drupal.attachBehaviors();
      });
    }
  };
})(jQuery);
