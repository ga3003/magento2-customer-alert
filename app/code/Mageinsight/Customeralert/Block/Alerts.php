<?php

namespace Mageinsight\Customeralert\Block;

class Alerts extends \Magento\Customer\Block\Account\Dashboard
{
    public function getIsSubscribedForSales() {
        $customer = $this->getCustomer();
        $salesAlert = $customer->getCustomAttribute('sales_alert')->getValue();
        if ($salesAlert) {
            return true;
        }
        
        return false;
    }
}
