<?php

namespace Pimgento\Entities\Helper;

use \Magento\Framework\App\Helper\AbstractHelper;

class Config extends AbstractHelper
{
    /** Config keys */
    const CONFIG_PIMGENTO_GENERAL_CSV_LINES_TERMINATED  = 'pimgento/general/lines_terminated';
    const CONFIG_PIMGENTO_GENERAL_CSV_FIELDS_TERMINATED = 'pimgento/general/fields_terminated';
    const CONFIG_PIMGENTO_GENERAL_CSV_FIELDS_ENCLOSURE  = 'pimgento/general/fields_enclosure';
    const CONFIG_PIMGENTO_GENERAL_LOAD_DATA_LOCAL       = 'pimgento/general/load_data_local';
    const CONFIG_PIMGENTO_GENERAL_DATA_INSERTION_METHOD = 'pimgento/general/data_insertion_method';
    const CONFIG_PIMGENTO_GENERAL_QUERY_NUMBER          = 'pimgento/general/query_number';

    /**
     * Data in file insertion method
     */
    const INSERTION_METHOD_DATA_IN_FILE = 'data_in_file';

    /**
     * By rows insertion method
     */
    const INSERTION_METHOD_BY_ROWS = 'by_rows';

    /**
     * Retrieve CSV configuration
     *
     * @return array
     */
    public function getCsvConfig()
    {
        return array(
            'lines_terminated'  => $this->scopeConfig->getValue(self::CONFIG_PIMGENTO_GENERAL_CSV_LINES_TERMINATED),
            'fields_terminated' => $this->scopeConfig->getValue(self::CONFIG_PIMGENTO_GENERAL_CSV_FIELDS_TERMINATED),
            'fields_enclosure'  => $this->scopeConfig->getValue(self::CONFIG_PIMGENTO_GENERAL_CSV_FIELDS_ENCLOSURE),
        );
    }

    /**
     * Retrieve Load Data Infile option
     *
     * @return int
     */
    public function getLoadDataLocal()
    {
        return $this->scopeConfig->getValue(self::CONFIG_PIMGENTO_GENERAL_LOAD_DATA_LOCAL);
    }

    /**
     * Retrieve insertion method
     *
     * @return string
     */
    public function getInsertionMethod()
    {
        return (string) $this->scopeConfig->getValue(self::CONFIG_PIMGENTO_GENERAL_DATA_INSERTION_METHOD);
    }

    /**
     * Retrieve query number for multiple insert
     *
     * @return int
     */
    public function getQueryNumber()
    {
        return (int) $this->scopeConfig->getValue(self::CONFIG_PIMGENTO_GENERAL_QUERY_NUMBER);
    }
}