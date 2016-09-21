<?php

namespace Drupal\hms_commerce\Plugin\Field\FieldFormatter;
use Drupal\Core\Field\FieldItemListInterface;

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

  public function viewElements(FieldItemListInterface $items, $langcode) {

    $elements = parent::viewElements($items, $langcode);

    if ($items->count() > 0) {

      // Attach url of current entity.
      //todo: Could use something along $entity->toUrl()->setAbsolute()->toString() but do not want the path alias.
      $entity = $items->getEntity();
      $field_name = $items->getName();
      $elements['#attached']['drupalSettings']['hms_commerce']['formatter_settings'][$this->widgetType][$field_name]['premium_content'] = [
        'url' => $GLOBALS['base_url']
          . '/' . $entity->getEntityTypeId()
          . '/' . $entity->id(),
        'id' => $entity->getEntityTypeId() . 'Id' . $entity->id(),
      ];
    }

    return $elements;
  }
}
