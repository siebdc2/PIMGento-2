# PIMGento2

PIMGento2 is a Magento 2 extension that allows you to import your catalog from Akeneo CSV files into Magento.

## Documentation

PIMGento complete documentation is available [here](doc/summary.md).

## Upgrade from Magento 2.1 to 2.2

Please refer to [Compatibility](doc/important_stuff/compatibility.md).

## How it works

PIMGento reads CSV files from Akeneo and insert data directly in Magento database.

In this way, it makes imports very fast and doesn't disturb your e-commerce website.

With PIMGento, you can import :
* Categories
* Families
* Attributes
* Options
* Variants (Akeneo < 2.0)
* Product Model (Akeneo >= 2.0)
* Family Variant (Akeneo >= 2.0)
* Products

## Requirements

* Akeneo PIM >= 1.3 (CE & EE)
* Magento >= 2.0 CE & EE
* Database encoding must be UTF-8

Only for MySQL LOAD DATA INFILE statement:

* Set local_infile mysql variable to TRUE
* Add "driver_options" key to Magento2 default connection configuration (app/etc/env.php)

```php
'db' =>
  array (
    'table_prefix' => '',
    'connection' =>
    array (
      'default' =>
      array (
        'host' => '',
        'dbname' => '',
        'username' => '',
        'password' => '',
        'active' => '1',
        'driver_options' => array(PDO::MYSQL_ATTR_LOCAL_INFILE => true),
      ),
    ),
  ),
```

If LOAD DATA INFILE statement is not authorized for security reasons, insertion row by row is possible.

## Installation, Configuration and Usage

If you want to know how to install, configure or use PIMGento, please check [how to...](doc/important_stuff/how_to.md) section. We advise you to start here!

## Roadmap

We have updated our roadmap. Just go [here](doc/important_stuff/roadmap.md).

## About us

Founded by lovers of innovation and design, [Agence Dn'D] (http://www.dnd.fr) assists companies for 11 years in the creation and development of customized digital (open source) solutions for web and E-commerce.
