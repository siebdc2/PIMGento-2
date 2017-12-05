<?php

namespace Pimgento\VariantFamily\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    /** Config keys */
    const CONFIG_PIMGENTO_VARIANT_FAMILY_MAX_AXES_NUMBER = 'pimgento/variant_family/max_axes_number';

    /**
     * Retrieve max axes number
     *
     * @return int
     */
    public function getMaxAxesNumber()
    {
        return (int) $this->scopeConfig->getValue(self::CONFIG_PIMGENTO_VARIANT_FAMILY_MAX_AXES_NUMBER);
    }
}