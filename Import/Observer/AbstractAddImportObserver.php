<?php

namespace Pimgento\Import\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\DataObject;
use Magento\Framework\Module\Manager;

abstract class AbstractAddImportObserver
{
    /**
     * @var EventManager $eventManager
     */
    protected $eventManager;

    /**
     * @var Manager $moduleManager
     */
    protected $moduleManager;

    /**
     * PHP Constructor
     *
     * @param EventManager $eventManager
     * @param Manager $moduleManager
     */
    public function __construct(
        EventManager $eventManager,
        Manager $moduleManager
    ) {
        $this->eventManager  = $eventManager;
        $this->moduleManager = $moduleManager;
    }

    /**
     * Add import to Collection
     *
     * @param \Magento\Framework\Event\Observer $observer
     */
    public function execute(Observer $observer)
    {
        /** @var $collection \Pimgento\Import\Model\Import\Collection */
        $collection = $observer->getEvent()->getCollection();

        $collection->addImport($this->getImportDefinition());
    }

    /**
     * Get the import definition
     *
     * @return array
     */
    protected function getImportDefinition()
    {
        return [
            'code'             => $this->getImportCode(),
            'name'             => $this->getImportName(),
            'class'            => $this->getImportClassName(),
            'sort_order'       => $this->getImportSortOrder(),
            'file_is_required' => $this->isImportFileRequired(),
            'steps'            => $this->getStepsDefinition()
        ];
    }

    /**
     * Get the import classname to use
     *
     * @return string
     */
    protected function getImportClassName()
    {
        $response = new DataObject();
        $response->setData('import_class', $this->getImportDefaultClassname());

        $this->eventManager->dispatch(
            'pimgento_' . $this->getImportCode() . '_import_class',
            ['response' => $response]
        );

        return $response->getData('import_class');
    }

    /**
     * Get additionnal steps to add
     *
     * @param string $eventPrefix
     * @param string $fieldName
     *
     * @return mixed
     */
    protected function getAdditionnalSteps($eventPrefix = 'add_final_steps', $fieldName = 'final_steps')
    {
        $response = new DataObject();
        $response->setData($fieldName, []);

        $this->eventManager->dispatch(
            'pimgento_' . $this->getImportCode() . '_import_' . $eventPrefix,
            ['response' => $response]
        );

        return $response->getData($fieldName);
    }

    /**
     * Is a file is required for this import
     *
     * @return bool
     */
    protected function isImportFileRequired()
    {
        return true;
    }

    /**
     * Get the import code
     *
     * @return string
     */
    abstract protected function getImportCode();

    /**
     * Get the import code
     *
     * @return string
     */
    abstract protected function getImportName();

    /**
     * Get the sort order
     *
     * @return int
     */
    abstract protected function getImportSortOrder();

    /**
     * Get the default import classname
     *
     * @return string
     */
    abstract protected function getImportDefaultClassname();

    /**
     * get the steps definition
     *
     * @return array
     */
    abstract protected function getStepsDefinition();
}
