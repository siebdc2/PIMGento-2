<?php

namespace Pimgento\VariantFamily\Model\Factory;

use \Pimgento\Import\Model\Factory;
use \Pimgento\Entities\Model\Entities;
use \Pimgento\Import\Helper\Config as helperConfig;
use \Pimgento\VariantFamily\Helper\Config as helperVariant;
use \Magento\Framework\Event\ManagerInterface;
use \Magento\Framework\App\Cache\TypeListInterface;
use \Magento\Eav\Model\Entity\Attribute\SetFactory;
use \Magento\Framework\Module\Manager as moduleManager;
use \Magento\Framework\App\Config\ScopeConfigInterface as scopeConfig;
use \Zend_Db_Expr as Expr;
use \Exception;

class Import extends Factory
{

    /**
     * @var Entities
     */
    protected $_entities;

    /**
     * @var TypeListInterface
     */
    protected $_cacheTypeList;

    /**
     * @var helperVariant
     */
    protected $_helperVariant;

    /**
     * @param \Pimgento\Entities\Model\Entities $entities
     * @param \Pimgento\Import\Helper\Config $helperConfig
     * @param \Pimgento\VariantFamily\Helper\Config $helperVariant
     * @param \Magento\Framework\Module\Manager $moduleManager
     * @param \Magento\Framework\App\Config\ScopeConfigInterface $scopeConfig
     * @param \Magento\Framework\Event\ManagerInterface $eventManager
     * @param \Magento\Framework\App\Cache\TypeListInterface $cacheTypeList
     * @param array $data
     */
    public function __construct(
        Entities $entities,
        helperConfig $helperConfig,
        helperVariant $helperVariant,
        moduleManager $moduleManager,
        scopeConfig $scopeConfig,
        ManagerInterface $eventManager,
        TypeListInterface $cacheTypeList,
        array $data = []
    )
    {
        parent::__construct($helperConfig, $eventManager, $moduleManager, $scopeConfig, $data);
        $this->_entities = $entities;
        $this->_cacheTypeList = $cacheTypeList;
        $this->_helperVariant = $helperVariant;
    }

    /**
     * Create temporary table
     */
    public function createTable()
    {
        $file = $this->getFileFullPath();

        if (!is_file($file)) {
            $this->setContinue(false);
            $this->setStatus(false);
            $this->setMessage($this->getFileNotFoundErrorMessage());
        } else {
            $this->_entities->createTmpTableFromFile($file, $this->getCode(), array('code'));
        }
    }

    /**
     * Insert data into temporary table
     */
    public function insertData()
    {
        $file = $this->getFileFullPath();

        $count = $this->_entities->insertDataFromFile($file, $this->getCode());

        $this->setMessage(
            __('%1 line(s) found', $count)
        );
    }

    /**
     * Update Axis column
     */
    public function updateAxis()
    {
        $resource = $this->_entities->getResource();
        $connection = $resource->getConnection();
        $tmpTable = $this->_entities->getTableName($this->getCode());

        $connection->addColumn($tmpTable, '_axis', [
            'type' => 'text',
            'length' => 255,
            'COMMENT' => ' ',
        ]);

        $columns = [];
        for ($i = 1; $i <= $this->_helperVariant->getMaxAxesNumber(); $i++) {
            $columns[] = 'variant-axes_' . $i;
        }

        foreach ($columns as $key => $column) {
            if (!$connection->tableColumnExists($tmpTable, $column)) {
                unset($columns[$key]);
            }
        }

        if (!empty($columns)) {
            $update = 'TRIM(BOTH "," FROM CONCAT(`' . join('`, "," ,`', $columns) . '`))';
            $connection->update($tmpTable, ['_axis' => new Expr($update)]);
        }

        $variantFamily = $connection->query(
            $connection->select()->from($tmpTable)
        );

        $attributes = $connection->fetchPairs(
            $connection->select()->from(
                $resource->getTable('eav_attribute'), array('attribute_code', 'attribute_id')
            )
            ->where('entity_type_id = ?', 4)
        );

        while (($row = $variantFamily->fetch())) {
            $axisAttributes = explode(',', $row['_axis']);

            $axis = [];

            foreach ($axisAttributes as $code) {
                if (isset($attributes[$code])) {
                    $axis[] = $attributes[$code];
                }
            }

            $connection->update($tmpTable, ['_axis' => join(',', $axis)], ['code = ?' => $row['code']]);
        }
    }

    /**
     * Update Product Model
     */
    public function updateProductModel()
    {
        $resource = $this->_entities->getResource();
        $connection = $resource->getConnection();
        $tmpTable = $this->_entities->getTableName($this->getCode());

        $query = $connection->select()
            ->from(false, ['axis' => 'f._axis'])
            ->joinLeft(
                ['f' => $tmpTable],
                'p.family_variant = f.code',
                []
            );

        $connection->query(
            $connection->updateFromSelect($query, ['p' => $resource->getTable('pimgento_variant')])
        );
    }

    /**
     * Drop temporary table
     */
    public function dropTable()
    {
        $this->_entities->dropTable($this->getCode());
    }

    /**
     * Clean cache
     */
    public function cleanCache()
    {
        $types = array(
            \Magento\Framework\App\Cache\Type\Block::TYPE_IDENTIFIER,
            \Magento\PageCache\Model\Cache\Type::TYPE_IDENTIFIER
        );

        foreach ($types as $type) {
            $this->_cacheTypeList->cleanType($type);
        }

        $this->setMessage(
            __('Cache cleaned for: %1', join(', ', $types))
        );
    }

    /**
     * Replace column name
     *
     * @param string $column
     * @return string
     */
    protected function _columnName($column)
    {
        $matches = array(
            'label' => 'name',
        );

        foreach ($matches as $name => $replace) {
            if (preg_match('/^'. $name . '/', $column)) {
                $column = preg_replace('/^'. $name . '/', $replace, $column);
            }
        }

        return $column;
    }

}