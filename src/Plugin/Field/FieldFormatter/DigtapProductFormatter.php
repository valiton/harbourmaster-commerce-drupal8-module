<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldFormatter;

/**
 * @FieldFormatter(
 *   id = "digtap_product",
 *   label = @Translation("Bestseller product"),
 *   field_types = {
 *     "digtap_product"
 *   }
 * )
 */
class DigtapProductFormatter extends DigtapFormatterBase {
  protected $widgetType = 'PremiumDownload';
}
