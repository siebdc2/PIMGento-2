<?php

namespace Pimgento\Product\Setup;

use Magento\Framework\Setup\UpgradeDataInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\App\Config\ReinitableConfigInterface;
use Magento\Framework\DB\FieldDataConverterFactory;
use Magento\Framework\DB\FieldDataConverter;
use Magento\Framework\DB\Select\QueryModifierFactory;
use Magento\Framework\DB\DataConverter\SerializedToJson;
use Pimgento\Product\Helper\Config as ConfigProductHelper;
use Pimgento\Product\Helper\Config;

/**
 * Upgrade Data script
 * @codeCoverageIgnore
 */
class UpgradeData implements UpgradeDataInterface
{
    /**
     * @var FieldDataConverterFactory
     */
    protected $fieldDataConverterFactory;

    /**
     * @var QueryModifierFactory
     */
    protected $queryModifierFactory;

    /**
     * @var \Magento\Framework\DB\Query\Generator
     */
    protected $queryGenerator;
    /**
     * @var ReinitableConfigInterface
     */
    private $reinitConfig;

    /**
     * Constructor
     *
     * @param FieldDataConverterFactory $fieldDataConverterFactory
     * @param QueryModifierFactory      $queryModifierFactory
     * @param ReinitableConfigInterface $reinitConfig
     */
    public function __construct(
        FieldDataConverterFactory $fieldDataConverterFactory,
        QueryModifierFactory      $queryModifierFactory,
        ReinitableConfigInterface $reinitConfig
    ) {
        $this->fieldDataConverterFactory = $fieldDataConverterFactory;
        $this->queryModifierFactory      = $queryModifierFactory;
        $this->reinitConfig              = $reinitConfig;
    }

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD.ExcessiveMethodLength)
     */
    public function upgrade(ModuleDataSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        if ($context->getVersion() && version_compare($context->getVersion(), '1.0.1', '<')) {
            $data = $installer->getConnection()->fetchOne(
                $installer->getConnection()->select()
                    ->from($installer->getTable('core_config_data'), array('value'))
                    ->where('path = ?', Config::CONFIG_PIMGENTO_PRODUCT_CONFIGURABLE_ATTR)
                    ->limit(1)
            );

            $matches = array();

            if ($data) {
                $attributes = explode(',', $data);

                foreach ($attributes as $attribute) {
                    $matches['_' . time() . '_' . uniqid()] = array(
                        'attribute' => $attribute,
                        'value'     => '',
                    );
                }
            }

            $installer->getConnection()->update(
                $installer->getTable('core_config_data'),
                array('value' => serialize($matches)),
                array('path = ?' => Config::CONFIG_PIMGENTO_PRODUCT_CONFIGURABLE_ATTR)
            );
        }

        /** Magento 2.2 Compatibility */
        if (version_compare($context->getVersion(), '1.1.0', '<')) {
            $this->convertSerializedDataToJson($setup);
        }

        $installer->endSetup();

    }
    /**
     * Convert the values from php serialize to json.
     *
     * @param ModuleDataSetupInterface $setup
     *
     * @return void
     */
    protected function convertSerializedDataToJson(ModuleDataSetupInterface $setup)
    {
        /** @var FieldDataConverter $fieldDataConverter */
        $fieldDataConverter = $this->fieldDataConverterFactory->create(SerializedToJson::class);

        $queryModifier = $this->queryModifierFactory->create(
            'in',
            [
                'values' => [
                    'path' => [
                        ConfigProductHelper::CONFIG_PIMGENTO_PRODUCT_ATTR_MAPPING,
                        ConfigProductHelper::CONFIG_PIMGENTO_PRODUCT_CONFIGURABLE_ATTR,
                    ]
                ]
            ]
        );

        $fieldDataConverter->convert(
            $setup->getConnection(),
            $setup->getTable('core_config_data'),
            'config_id',
            'value',
            $queryModifier
        );

        $this->reinitConfig->reinit();
    }
}
