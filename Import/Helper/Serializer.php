<?php

namespace Pimgento\Import\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Serializer extends AbstractHelper
{

    /**
     * Unserialize data from config (keep compatibility with Magento < 2.2)
     * This will be replaced by \Magento\Framework\Serialize\Serializer\Json in some time
     *
     * @return array
     */
    public function unserialize($value)
    {
        $data = [];

        if (!$value) {
            return $data;
        }

        try {
            $data = unserialize($value);
        } catch (\Exception $e) {
            $data = [];
        }

        if (empty($data) && json_decode($value)) {
            $data = json_decode($value, true);
        }

        return $data;
    }
}