Compatibility with...
=====================

Akeneo
------

* PIMGento2 is compatible with the [Enhanced Connector Bundle](https://github.com/akeneo-labs/EnhancedConnectorBundle) for the following versions of Akeneo:
    + v2.0.*
    + v1.7.*
    + v1.6.*
    + v1.5.*
    + v1.4.*
    + v1.3.*

* Among Akeneo versions, Enhanced Connector Bundle files can be integrated natively. For example, if your PIM is in 1.7, the Enhanced Connector Bundle only export family and attribute, but don't worry it's because others files are now natively compatible with PIMGento2, so you can just use natives exports!

* PIMGento2 **is not compatible** with the [Inner Variation Bundle](https://marketplace.akeneo.com/package/inner-variation-bundle-ee-only).

* PIMGento2 is compatible with both Akeneo CE and EE.

Magento2
--------

* PIMGento2 is compatible with Magento2 following this schema:

| Magento Version | PIMGento2 Version |
|-----------------|-------------------|
| =< 2.3.x        | master            |

Upgrade from Magento 2.1 to 2.2
-------------------------------

On Magento 2.2, enable *Pimgento_Upgrade* module in Magento:

```shell
php bin/magento module:enable Pimgento_Upgrade
```

Check and update database setup:

```shell
php bin/magento setup:db:status
php bin/magento setup:upgrade
```

Disable *Pimgento_Upgrade*:

```shell
php bin/magento module:disable Pimgento_Upgrade
```