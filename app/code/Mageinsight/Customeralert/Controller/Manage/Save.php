<?php

namespace Mageinsight\Customeralert\Controller\Manage;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Api\CustomerRepositoryInterface as CustomerRepository;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Api\Data\CustomerInterface;

class Save extends \Magento\Customer\Controller\AbstractAccount
{
    /**
     * @var \Magento\Framework\View\Result\PageFactory
     */
    protected $resultPageFactory;

    /**
     * @var \Magento\Customer\Model\Session
     */
    protected $customerSession;

    /**
     * @var CustomerRepository
     */
    protected $customerRepository;

    /**
     * @var StoreManagerInterface
     */
    protected $storeManager;

    /**
     * Download constructor.
     * @param Context $context
     * @param FileFactory $fileFactory
     * @param Csv $csv
     * @param DirectoryList $directoryList
     */
    public function __construct(
        Context $context,
        \Magento\Framework\View\Result\PageFactory $resultPageFactory,
        \Magento\Customer\Model\Session $customerSession,
        CustomerRepository $customerRepository,
        StoreManagerInterface $storeManager
    ){
        $this->resultPageFactory = $resultPageFactory;
        $this->customerSession = $customerSession;
        $this->customerRepository = $customerRepository;
        $this->storeManager = $storeManager;
        parent::__construct($context);
    }

    /**
     * Manage Alerts subscribed for the customers
     *
     * @return void
     */
    public function execute()
    {
        $params = $this->getRequest()->getParams();
        if (empty($params)) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving your subscription.'));
            return;
        }

        $customerId = $this->customerSession->getCustomerId();
        if ($customerId === null) {
            $this->messageManager->addErrorMessage(__('Something went wrong while saving your subscription.'));
        } else {
            try {
                $customer = $this->customerRepository->getById($customerId);
                $storeId = (int)$this->storeManager->getStore()->getId();
                $customer->setStoreId($storeId);
                $isSubscribedState = $customer->getCustomAttribute('sales_alert');
                $salesAlert = !($params['sales_alert'] == 'false');
                if ($salesAlert !== $isSubscribedState) {
                    $customer->setCustomAttribute('sales_alert', $salesAlert);
                    $this->customerRepository->save($customer);
                    $this->messageManager->addSuccess(__('Your subscripition is saved successfully.'));
                } else {
                    $this->messageManager->addSuccess(__('Your subscripition is updated successfully.'));
                }
            } catch (\Exception $e) {
                $this->messageManager->addErrorMessage(__('Something went wrong while saving your subscription.'));
            }
        }
    }
}