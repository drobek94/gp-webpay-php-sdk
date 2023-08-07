<?php

namespace AdamStipak\Webpay;

use AdamStipak\Webpay\PaymentRequest\AddInfo;

/**
 * Payment Requester class
 */
class PaymentRequest {

  const EUR = 978;
  const CZK = 203;
  const GBP = 826;
  const HUF = 348;
  const PLN = 985;
  const RUB = 643;
  const USD = 840;

  const APPLE_PAY = 'APAY';
  const EPS = 'EPS';
  const GOOGLE_PAY = 'GPAY';
  const KLARNA = 'KLARNA';
  const PAYMENT_CARD = 'CRD';
  const PAYSAFECARD = 'PAYSAFECARD';
  const PLATBA_24 = 'BTNCS';
  const SEPADIRECTDEBIT = 'SEPADIRECTDEBIT';
  const SOFORT = 'SOFORT';

  /** @var array */
  private $params = [];

  /**
   * Payment Requester
   *
   * @param int $orderNumber Payments number - must be in each request from trader unique.
   * @param float $amount Price to pay
   * @param int $currency Currency code ISO 4217
   * @param int $depositFlag Request Indicates whether the payment is to be paid automatically. Allowed values: 0 = no immediate payment required 1 = payment is required
   * @param string $url Full Merchant URL. A result will be sent to this address  request. The result is forwarded over customer browser
   * @param string|null $merOrderNumber Order Number. In case it is not specified, it will be used  value $orderNumber It will appear on the bank statement.
   * @param string|null $md Any merchant data.
   */
  public function __construct (
    int     $orderNumber,
    float   $amount,
    int     $currency,
    int     $depositFlag,
    string  $url,
    string  $merOrderNumber = null,
    string  $md = null,
    AddInfo $addInfo = null,
	string  $paymentMethod = self::PAYMENT_CARD,
	string  $description = null,
	string  $userparam1 = null
  ) {
    $this->params['MERCHANTNUMBER'] = "";
    $this->params['OPERATION'] = 'CREATE_ORDER';
    $this->params['ORDERNUMBER'] = $orderNumber;
    $this->params['AMOUNT'] = $amount * 100;
    $this->params['CURRENCY'] = $currency;
    $this->params['DEPOSITFLAG'] = $depositFlag;

    if ($merOrderNumber) {
      $this->params['MERORDERNUM'] = $merOrderNumber;
    }

    $this->params['URL'] = $url;

	if ($description !== null) {
	  $this->params['DESCRIPTION'] = $description;
	}

	if ($md !== null) {
      $this->params['MD'] = $md;
	}

	if ($userparam1 !== null) {
      $this->params['USERPARAM1'] = $userparam1;
	}

    $this->params['PAYMETHOD'] = $paymentMethod;

    if ($addInfo !== null) {
      $this->params['ADDINFO'] = $addInfo->toXml();
    }
  }

  /**
   * Set Digest for current request
   *
   * @param string $digest Verification signature of the string that is generated by concatenating all fields in the order given.
   * @internal
   */
  public function setDigest ($digest) {
    $this->params['DIGEST'] = $digest;
  }

  /**
   * Gives You all Request params
   * @return array
   */
  public function getParams (): array {
    return $this->params;
  }

  public function getSignParams (): array {
    $filterOutParams = ['LANG'];

    return array_filter($this->params, function (string $key) use ($filterOutParams) {
      return !in_array($key, $filterOutParams);
    }, ARRAY_FILTER_USE_KEY);
  }

  /**
   * Sets a value for a param key
   * @param string $key
   * @param $value
   */
  public function setParam ($key, $value) {
    $this->params[$key] = $value;
  }

  /**
   * Set The Merchant Number for request
   *
   * @param string $number Attributed merchant number.
   * @internal
   */
  public function setMerchantNumber ($number) {
    $this->params['MERCHANTNUMBER'] = $number;
  }

  /**
   * Add Description parameter to request fields
   *
   * @param string $value field value
   */
  public function setDescription ($value) {
    $this->params['DESCRIPTION'] = $value;
  }
}
