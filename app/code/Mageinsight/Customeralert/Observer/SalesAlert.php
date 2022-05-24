<?php
namespace Mageinsight\Customeralert\Observer;

class SalesAlert implements \Magento\Framework\Event\ObserverInterface
{
    protected $_customerRepositoryInterface;

    protected $logger;

    public function __construct(
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepositoryInterface,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Psr\Log\LoggerInterface $logger
    ) {
        $this->_customerRepositoryInterface = $customerRepositoryInterface;
        $this->_customerFactory = $customerFactory;
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer) {
        $customer = $observer->getCustomer();
        if ($customer->getId()) {
            try {
                $customer->setCustomAttribute('sales_alert', 1);
                $this->_customerRepositoryInterface->save($customer);
            } catch (\Exception $e) {
                $this->logger->error(__("Something went wrong while adding slaes_alert value: %1", $e->getMessage()));
            }
        }
    }
}