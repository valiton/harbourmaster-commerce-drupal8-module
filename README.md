
Configuration
-----------

  * After installing the module, set the Bestseller API URL and entitlement
  group name on page admin/config/hmscommerce.

  * If not already set, also provide the usermanager API URL on page
  admin/people/hms.


Premium content
-----------
### Configuration

  * To add premium functionality to an entity type, add the 'Premium content'
   field to it.
  * On the field settings page other fields added to that entity type can be
   marked as premium fields and will be encrypted on entity view pages.
  * The field settings page also allows to mark any field as teaser. This field
   will be shown on entity view pages instead of the content rendered by premium
   fields.

### Usage

In order to make an entity of that type premium, check the 'Premium content'
checkbox on the entity edit page and set the price category. The price
categories are populated from Bestseller. The entity must have a price category
other than '- None -', otherwise it is not premium.


Premium download
-----------
### Configuration

  * To display products on an entity, add the 'Bestseller product' field to it.

### Usage
For products to display on an entity view page, populate the 'Bestseller
product' field with products on the entity edit page (not yet implemented).
