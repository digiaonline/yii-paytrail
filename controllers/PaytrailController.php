<?php
/**
 * PaytrailController class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2014
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-paytrail.controllers
 */

class PaytrailController extends PaymentController
{
    /**
     * @var string
     */
    public $managerId = 'payment';

    /**
     * @var PaymentTransaction
     */
    private $_transaction;

    /**
     * @var PaytrailGateway
     */
    private $_gateway;

    /**
     * @return array
     */
    public function filters()
    {
        return array(
            'validateSuccessRequest + success, notify',
            'validateFailureRequest + failure',
        );
    }

    /**
     * Insures that successful payment requests have a valid authentication code in the GET params.
     * @param CFilterChain $filterChain the filter chain.
     * @throws CException if the authentication code does not match the passed data.
     */
    public function filterValidateSuccessRequest(CFilterChain $filterChain)
    {
        $request = Yii::app()->getRequest();
        $ORDER_NUMBER = $request->getQuery('ORDER_NUMBER');
        $TIMESTAMP = $request->getQuery('TIMESTAMP');
        $PAID = $request->getQuery('PAID');
        $METHOD = $request->getQuery('METHOD');
        $RETURN_AUTHCODE = $request->getQuery('RETURN_AUTHCODE');

        $transaction = $this->loadTransaction($ORDER_NUMBER);
        $gateway = $this->createGateway($transaction->gateway);
        $data = implode('|', array($ORDER_NUMBER, $TIMESTAMP, $PAID, $METHOD, $gateway->apiSecret));
        if (!$this->validateAuthCode($RETURN_AUTHCODE, $data)) {
            throw new CException('Invalid authentication code.');
        }
        $filterChain->run();
    }

    /**
     * Insures that failed payment requests have a valid authentication code in the GET params.
     * @param CFilterChain $filterChain the filter chain.
     * @throws CException if the authentication code does not match the passed data.
     */
    public function filterValidateFailureRequest(CFilterChain $filterChain)
    {
        $request = Yii::app()->getRequest();
        $ORDER_NUMBER = $request->getQuery('ORDER_NUMBER');
        $TIMESTAMP = $request->getQuery('TIMESTAMP');
        $RETURN_AUTHCODE = $request->getQuery('RETURN_AUTHCODE');

        $transaction = $this->loadTransaction($ORDER_NUMBER);
        $gateway = $this->createGateway($transaction->gateway);
        $data = implode('|', array($ORDER_NUMBER, $TIMESTAMP, $gateway->apiSecret));
        if (!$this->validateAuthCode($RETURN_AUTHCODE, $data)) {
            throw new CException('Invalid authentication code.');
        }
        $filterChain->run();
    }

    /**
     * Invoked by Paytrail after a successful payment.
     * @param string $ORDER_NUMBER
     * @param string $TIMESTAMP
     * @param string $PAID
     * @param string $METHOD
     * @param string $RETURN_AUTHCODE
     */
    public function actionSuccess($ORDER_NUMBER, $TIMESTAMP, $PAID, $METHOD, $RETURN_AUTHCODE)
    {
        $manager = $this->getPaymentManager();
        $transaction = $this->loadTransaction($ORDER_NUMBER);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_SUCCEEDED, $transaction);
        $context = $manager->resolveContext($transaction->context);
        $this->redirect($context->successUrl);
    }

    /**
     * Invoked by Paytrail after a failed payment.
     * @param string $ORDER_NUMBER
     * @param string $TIMESTAMP
     * @param string $RETURN_AUTHCODE
     */
    public function actionFailure($ORDER_NUMBER, $TIMESTAMP, $RETURN_AUTHCODE)
    {
        $manager = $this->getPaymentManager();
        $transaction = $this->loadTransaction($ORDER_NUMBER);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_FAILED, $transaction);
        $context = $manager->resolveContext($transaction->context);
        $this->redirect($context->failureUrl);
    }

    /**
     * Invoked by Paytrail when a payment has been confirmed.
     * @param string $ORDER_NUMBER
     * @param string $TIMESTAMP
     * @param string $PAID
     * @param string $METHOD
     * @param string $RETURN_AUTHCODE
     */
    public function actionNotify($ORDER_NUMBER, $TIMESTAMP, $PAID, $METHOD, $RETURN_AUTHCODE)
    {
        $manager = $this->getPaymentManager();
        $transaction = $this->loadTransaction($ORDER_NUMBER);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_COMPLETED, $transaction);
        Yii::app()->end();
    }

    /**
     * Loads the order transaction instance based on order number.
     * @param int|string $orderNumber the order number.
     * @return PaymentTransaction the transaction.
     * @throws CException if the transaction cannot be found based on the order number.
     */
    protected function loadTransaction($orderNumber)
    {
        if ($this->_transaction !== null) {
            return $this->_transaction;
        }
        $transaction = CActiveRecord::model($this->getPaymentManager()->transactionClass)->findByAttributes(
            array('orderIdentifier' => $orderNumber)
        );
        if ($transaction === null) {
            throw new CException(sprintf('Failed to load payment transaction with order identifier #%d.', $orderNumber));
        }
        return $this->_transaction = $transaction;
    }

    /**
     * Creates a payment gateway instance.
     * @param string $name the name of the gateway to create.
     * @return PaytrailGateway the gateway.
     */
    protected function createGateway($name)
    {
        if ($this->_gateway !== null) {
            return $this->_gateway;
        }
        return $this->_gateway = $this->getPaymentManager()->createGateway($name);
    }

    /**
     * Validates that the authentication code passed by Paytrail is valid.
     * @param string $code the code passed from Paytrail.
     * @param string $data the data that the code should consists of.
     * @return bool if the codes match.
     */
    protected function validateAuthCode($code, $data)
    {
        return $code === strtoupper(md5($data));
    }
}