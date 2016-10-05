Harbourmaster commerce
======================

CONTENTS OF THIS FILE
---------------------

 * Introduction
 * Requirements
 * Installation
 * Configuration
 * Usage
 * Maintainers

INTRODUCTION
------------

This module integrates the Bestseller commerce software with Drupal providing a
way of monetizing content created with it.

It does so by allowing an editor to set any content piece ("Drupal entity") or
rather any of its parts ("Entity fields") as premium. These pieces of content
are encrypted by the module and decrypted on the fly if an authenticated
user is entitled to view them (e.g by having purchased a piece of content or a
subscription). In case a user is not entitled to view the premium content, they
are presented a teaser and a widget allowing to buy it.
The purchase is done with React widgets (rendered by Drupal) which talk to the
Bestseller API.

It is also possible to display Bestseller products on any entity by using a
built in product browser. The products are then displayed along with their
crossselling products and can be purchased through said react widgets.

The implementation of these two functionalities as Drupal fields provides a high
flexibility by allowing site builders to take advantage of various Drupal APIs
and by allowing other modules to change this module's behaviour.

REQUIREMENTS
------------

This module requires the following modules:

 * Harbourmaster (https://www.drupal.org/sandbox/patrick_durold/2791755)

INSTALLATION
------------

See https://www.drupal.org/documentation/install/modules-themes/modules-8
for instructions on how to install or update Drupal modules.

CONFIGURATION
-------------

After installing the module, go to admin/config/hmscommerce and provide

  * the URL to the Bestseller installation,

  * the name of the entitlement the user will be given upon purchasing a
    subscription,

  * the shared secret key between the Usermanager and this Drupal instance to
    encrypt the premium content pieces,

  * the generic error message which will be shown to end users when premium
    content cannot be shown,

  * if not already set, also provide the usermanager API URL on the page
    admin/people/harbourmaster.

USAGE
-----

### Premium content

#### Configuration - site builders

  * To add premium functionality to an entity type, add the 'Premium content'
    field to it.

    - admin/structure/types/manage/[entity type]/fields

    - See https://www.drupal.org/docs/7/nodes-content-types-and-fields/working-with-content-types-and-fields-drupal-7-and-later
      to learn what can be done with Drupal fields.

  * Keep the default 'Allowed number of values' setting of '1'.

  * On the field settings page other fields added to that entity type can be
    marked as premium fields. The output of these fields will be encrypted and
    hidden on entity view pages for users who are not entitled to view the
    premium content.

    - At least one field has to be marked premium, otherwise no content will be
      encrypted or hidden.

  * The field settings page also allows to mark fields as teasers. The output of
    these fields will be shown on entity view pages to users who are not
    entitled to view the premium content.

    - This setting is optional: If no field is marked as teaser, no teaser will
      be shown when viewing a premium entity of this entity type.

#### Usage - content editors

  * In order to make an entity of that type premium, check the premium content
    field's checkbox on the entity add/edit page and set the price category. The
    price categories are populated from Bestseller.

  * The entity must have a price category other than '- None -', otherwise it is
    not premium.

  * The price category cannot be changed if there is no connection to
    Bestseller. In that case it is only possible to keep the current category,
    or unset it, making the entity non-premium.

### Premium download

#### Configuration - site builders

  * To display Bestseller products on an entity, add the 'Bestseller product'
    field to it.

    - admin/structure/types/manage/[entity type]/fields

    - See https://www.drupal.org/docs/7/nodes-content-types-and-fields/working-with-content-types-and-fields-drupal-7-and-later
      to learn what can be done with Drupal fields.

  * It is advisable to set the field to hold multiple values so that multiple
    products can be added.

#### Usage - content editors

  * For Bestseller products to display on an entity view page, add or edit an
    entity with the 'Bestseller product' field in it and populate the field with
    products.

MAINTAINERS
-----------

Current maintainers:

 * Pawel Ginalski (gbyte.co) - https://www.drupal.org/u/gbyte.co
