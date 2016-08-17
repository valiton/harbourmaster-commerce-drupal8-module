/**
 * @file
 * Triggers all Drupal js behaviours after the decryption of content.

 */
(function($) {

  "use strict";

  Drupal.behaviors.hms_commercePremiumContent = {
    attach: function(context, settings) {

      // Triggers all Drupal js behaviours after the decryption of content.
      window.hmsAccess.setOnUpdateHandler(function() {
            Drupal.attachBehaviors(); //todo: Might want to limit this to encrypted DOM element.
      });
    }
  };
})(jQuery);
