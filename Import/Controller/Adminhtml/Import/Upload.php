<?php
/**
 *
 * Copyright © 2015 Magento. All rights reserved.
 * See COPYING.txt for license details.
 */
namespace Pimgento\Import\Controller\Adminhtml\Import;

use Magento\Backend\App\Action;
use \Magento\Framework\App\RequestInterface;
use \Magento\Backend\App\Action\Context;
use \Magento\Framework\Controller\Result\RawFactory;
use \Magento\Framework\Data\Form\FormKey;
use \Pimgento\Import\Helper\Config as configHelper;
use Magento\Framework\Stdlib\DateTime\DateTime;

class Upload extends Action
{
    /**
     * @var \Magento\Framework\Controller\Result\RawFactory
     */
    protected $resultRawFactory;

    /**
     * @var \Magento\Framework\Data\Form\FormKey
     */
    protected $_formKey;

    /**
     * @var \Pimgento\Import\Helper\Config
     */
    protected $_helperConfig;

    /** @var DateTime $datetime */
    protected $datetime;

    /**
     * @param \Magento\Framework\App\RequestInterface $request
     * @return \Magento\Framework\App\ResponseInterface
     */
    public function dispatch(RequestInterface $request)
    {
        $this->getRequest()->setParams(
            array('form_key' => $this->_formKey->getFormKey())
        );

        return parent::dispatch($request);
    }

    /**
     * @param \Magento\Backend\App\Action\Context $context
     * @param \Magento\Framework\Controller\Result\RawFactory $resultRawFactory
     * @param \Magento\Framework\Data\Form\FormKey $formKey
     * @param \Pimgento\Import\Helper\Config $helperConfig
     * @param DateTime $datetime
     */
    public function __construct(
        Context $context,
        RawFactory $resultRawFactory,
        FormKey $formKey,
        configHelper $helperConfig,
        DateTime $datetime
    ) {
        parent::__construct($context);
        $this->_helperConfig = $helperConfig;
        $this->resultRawFactory = $resultRawFactory;
        $this->_formKey = $formKey;
        $this->datetime = $datetime;
    }

    /**
     * @return \Magento\Framework\Controller\Result\Raw
     */
    public function execute()
    {
        try {
            $fileId = 'file';

            $file = $_FILES[$fileId];

            /** @var $uploader \Magento\MediaStorage\Model\File\Uploader */
            $uploader = $this->_objectManager->create(
                'Magento\MediaStorage\Model\File\Uploader',
                ['fileId' => $fileId]
            );
            $uploader->setAllowedExtensions(['txt', 'csv']);
            $uploader->setAllowRenameFiles(true);

            $file = $this->datetime->date('YmdHis') . '-' . $file['name'];

            $result = $uploader->save($this->_helperConfig->getUploadDir(), $file);

            unset($result['tmp_name']);
            unset($result['path']);
        } catch (\Exception $e) {
            $result = ['error' => $e->getMessage(), 'errorcode' => $e->getCode()];
        }

        /** @var \Magento\Framework\Controller\Result\Raw $response */
        $response = $this->resultRawFactory->create();
        $response->setHeader('Content-type', 'text/plain');
        $response->setContents(json_encode($result));
        return $response;
    }

    /**
     * @return bool
     */
    protected function _isAllowed()
    {
        return $this->_authorization->isAllowed('Pimgento_Import::pimgento_import');
    }

}
