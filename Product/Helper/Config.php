<?php

namespace Pimgento\Product\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;
use \Magento\Framework\App\Helper\Context;
use \Magento\Framework\Serialize\Serializer\Json;
use \Magento\Store\Model\StoreManagerInterface;

class Config extends AbstractHelper
{
    /** Config keys */
    const CONFIG_PIMGENTO_PRODUCT_ATTR_MAPPING      = 'pimgento/product/attribute_mapping';
    const CONFIG_PIMGENTO_PRODUCT_CONFIGURABLE_ATTR = 'pimgento/product/configurable_attributes';

    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var Json
     */
    protected $serializer;

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param StoreManagerInterface $storeManager
     * @param Json                  $serializer
     */
    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Json $serializer
    )
    {
        $this->_storeManager = $storeManager;
        $this->serializer = $serializer;
        parent::__construct($context);
    }

    /**
     * Retrieve stores default tax class
     *
     * @return array
     */
    public function getProductTaxClasses()
    {
        $classes = $this->scopeConfig->getValue('pimgento/product/tax_class');

        $result = array();

        $stores = $this->_storeManager->getStores(true);

        if ($classes) {
            $classes = $this->serializer->unserialize($classes);
            if (is_array($classes)) {
                foreach ($classes as $class) {

                    if ($this->getDefaultWebsiteId() == $class['website']) {
                        $result[0] = $class['tax_class'];
                    }

                    foreach ($stores as $store) {
                        if ($store->getWebsiteId() == $class['website']) {
                            $result[$store->getId()] = $class['tax_class'];
                        }
                    }
                }
            }
        }

        return $result;
    }

    /**
     * Retrieve default website id
     *
     * @return int
     */
    public function getDefaultWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

}