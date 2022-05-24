<?php

namespace Mageinsight\Customeralert\Model\Order\Email;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Mail\Template\TransportBuilderByStore;
use Magento\Sales\Model\Order\Email\Container\IdentityInterface;
use Magento\Sales\Model\Order\Email\Container\Template;
use Magento\Customer\Api\CustomerRepositoryInterface;

class SenderBuilder extends \Magento\Sales\Model\Order\Email\SenderBuilder
{
    /**
     * @var CustomerRepositoryInterface
     */
    protected $customerRepository;

    public function __construct(
        Template $templateContainer,
        IdentityInterface $identityContainer,
        TransportBuilder $transportBuilder,
        TransportBuilderByStore $transportBuilderByStore = null,
        CustomerRepositoryInterface $customerRepository
    ) {
        parent::__construct($templateContainer, $identityContainer, $transportBuilder, $transportBuilderByStore);
        $this->customerRepository = $customerRepository;
    }

    /**
     * Overriide send e-mail function to add condition based on customer.
     */
    public function send()
    {
        $customerEmail = $this->identityContainer->getCustomerEmail();
        $customer = $this->customerRepository->get($customerEmail);
        $customerSalesAlert = $customer->getCustomAttribute('sales_alert');
        $isSubscribedForSalesAlert = !empty($customerSalesAlert) ? $customerSalesAlert->getValue() : 1;
        if ($isSubscribedForSalesAlert) {
            $this->configureEmailTemplate();

            $this->transportBuilder->addTo(
                $this->identityContainer->getCustomerEmail(),
                $this->identityContainer->getCustomerName()
            );

            $copyTo = $this->identityContainer->getEmailCopyTo();

            if (!empty($copyTo) && $this->identityContainer->getCopyMethod() == 'bcc') {
                foreach ($copyTo as $email) {
                    $this->transportBuilder->addBcc($email);
                }
            }

            $transport = $this->transportBuilder->getTransport();
            $transport->sendMessage();
        }
    }
}