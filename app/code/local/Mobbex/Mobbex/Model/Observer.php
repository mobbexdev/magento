<?php

class Mobbex_Mobbex_Model_Observer
{
	/** @var Mobbex_Mobbex_Helper_Instantiator */
	public $instantiator;

	/** Flag to stop observer executing more than once */
	static protected $_singletonFlag = false;

	public function __construct()
	{
		// Init class properties
		\Mage::helper('mobbex/instantiator')->setProperties($this, ['settings', 'customField', 'mobbexTransaction', '_checkoutSession']);
	}

	/**
	 * Add a new tab in the backoffice category page.
	 * 
	 * @param Varien_Event_Observer $observer
	 */
	public function newTabCategory($observer)
	{
		if (self::$_singletonFlag)
			return;

		self::$_singletonFlag = true;

		$tabs = $observer->getTabs();

		// Create Mobbex Options tab
		$tabs->addTab('mobbex_configuration', [
			'label'   => 'Mobbex Options',
			'content' => $tabs->getLayout()->createBlock('mobbex/adminhtml_catalog_category_tab')->toHtml()
		]);
	}

	public function saveProductTabData()
	{
		if (self::$_singletonFlag)
			return;

		self::$_singletonFlag = true;

		$id = Mage::registry('current_product') ? Mage::registry('current_product')->getId() : false;

		// Exit if it's associated products save
		if (empty($id))
			return;

		$this->settings->saveCatalogSettings($id);
	}

	public function saveCategoryTabData()
	{
		if (self::$_singletonFlag)
			return;

		self::$_singletonFlag = true;

		$id = Mage::registry('current_category') ? Mage::registry('current_category')->getId() : false;

		if (empty($id))
			return;

		$this->settings->saveCatalogSettings($id, 'category');
	}

	/**
	 * Save dni from checkout billing information
	 */
	public function saveMobbexDni($observer)
	{
		$data = $observer->getEvent()->getControllerAction()->getRequest()->getPost('billing', array());
		$customerData = $this->_checkoutSession->getCustomer();

		if ($customerData && !empty($data['dni']))
			$this->customField->saveCustomField($customerData->getId(), 'customer', 'dni', $data['dni']);

		return true;
	}

	/**
	 * Calculates the refund amount of an order
	 * @param $observer : Varien_Event_Observer
	 * @return boolean
	 */
	public function informRefundData(Varien_Event_Observer $observer)
	{
		if (!self::$_singletonFlag) {
			$result = false;
			self::$_singletonFlag = true;
			$creditmemo = $observer->getEvent()->getCreditmemo();
			$order = $observer->getEvent()->getCreditmemo()->getOrder();
			$orderId = $order->getData('increment_id');
			$data = $this->mobbexTransaction->getMobbexTransaction($orderId, [true, true]); //get transaction data
			if (isset($data['data'])) {
				$payment = $order->getPayment();
				$transactionId = $payment->getData('last_trans_id');
				$amount = $creditmemo->getData('grand_total');
				$result = $this->sendRefund($transactionId, $amount);
			}

			return $result;
		}
	}

	/**
	 * Handle an order refund total and partial
	 * @param	$transactionId : integer
	 * @param	$amount : real
	 * @return	 boolean
	 */
	private function sendRefund($transactionId, $amount)
	{
		try {

			$result = \Mobbex\Api::request([
				'method' => 'POST',
				'uri'    => "operations/" . $transactionId . '/refund',
				'body'   => json_encode(['total' => floatval($amount)])
			]) ?: [];

			return !empty($result);
		} catch (\Exception $e) {
			$this->logger->debug('error', $e->getMessage(), isset($e->data) ? $e->data : []);
		}
	}
}
