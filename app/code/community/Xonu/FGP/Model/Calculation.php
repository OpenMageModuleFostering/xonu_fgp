<?php
/**
* @copyright (c) 2012, Pawel Kazakow <support@xonu.de>
* @license http://xonu.de/license/ xonu.de EULA
*/
class Xonu_FGP_Model_Calculation extends Mage_Tax_Model_Calculation
{
	public function getRateOriginRequest($store = null)
	{
		// set origin to destination
		
		$request = new Varien_Object();
		
		$session = Mage::getSingleton('checkout/session');
		if($session->hasQuote()) // getQuote() would lead to infinite loop here when switching currency
		{
			// use quote destination if quote exists
			
			$quote = $session->getQuote(); 
			$request = $this->getRateRequest(
				$quote->getShippingAddress(),
				$quote->getBillingAddress(),
				$quote->getCustomerTaxClassId(),
				$store
			);
			
			return $request;
		}
		else // quote is not available when switching the currency
		{
			return $this->getDefaultDestination();
		}
	}
	
	private function getDefaultDestination($store = null)
	{
		$address = new Varien_Object();
		$request = new Varien_Object();
	
		$address
			->setCountryId(Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_COUNTRY, $store))
			->setRegionId(Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_REGION, $store))
			->setPostcode(Mage::getStoreConfig(Mage_Tax_Model_Config::CONFIG_XML_PATH_DEFAULT_POSTCODE, $store));

		$customerTaxClass = null;
		$customer = $this->getCustomer();
		
        if (is_null($customerTaxClass) && $customer) {
            $customerTaxClass = $customer->getTaxClassId();
        } elseif (($customerTaxClass === false) || !$customer) {
            $customerTaxClass = $this->getDefaultCustomerTaxClass($store);
        }			
		
        $request
            ->setCountryId($address->getCountryId())
            ->setRegionId($address->getRegionId())
            ->setPostcode($address->getPostcode())
            ->setStore($store)
            ->setCustomerClassId($customerTaxClass);
		
		return $request;
	}
}
