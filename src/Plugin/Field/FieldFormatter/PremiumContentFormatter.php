<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldFormatter;

/**
 * @FieldFormatter(
 *   id = "premium_content",
 *   label = @Translation("Price category"),
 *   field_types = {
 *     "premium_content"
 *   }
 * )
 */
class PremiumContentFormatter extends DigtapFormatterBase {
  protected $widgetType = 'PremiumContent';
}
