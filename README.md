
Configuration
-----------
After installing the module, got to admin/config/hmscommerce and provide
  * the Bestseller API URL,
  * the entitlement group name,
  * the shared secret key used to encrypt the premium content pieces,
  * the generic error message which will be shown to end users when something
    goes wrong,
  * if not already set, also provide the usermanager API URL on the page
    admin/people/hms.

Premium content
-----------
### Configuration

  * To add premium functionality to an entity type, add the 'Premium content'
    field to it.
  * Set the field to hold only one value.
  * On the field settings page other fields added to that entity type can be
    marked as premium fields and will be encrypted on entity view pages.
  * The field settings page also allows to mark fields as teasers. These fields
    will be shown on entity view pages instead of the content rendered by
    premium fields for users not entitled to view the premium content.

### Usage

  * In order to make an entity of that type premium, check the 'Premium content'
    checkbox on the entity add or edit page and set the price category. The
    price categories are populated from Bestseller.
  * The entity must have a price category other than '- None -', otherwise it is
    not premium.
  * The price category cannot be changed if there is no connection to
    Bestseller. In that case it is only possible to keep the current category,
    or unset it.

Premium download
-----------
### Configuration

  * To display products on an entity, add the 'Bestseller product' field to it.
  * Set the field to hold multiple values so multiple products can be added.

### Usage

  * For products to display on an entity view page, add or edit an entity with
    the 'Bestseller product' field and populate the field with products.
