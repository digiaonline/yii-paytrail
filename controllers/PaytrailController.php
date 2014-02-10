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
     * @return array
     */
    public function filters()
    {
        return array(
            'accessControl',
            'validateSuccessRequest + success, notify',
            'validateFailureRequest + failure',
        );
    }

    /**
     * @param CFilterChain $filterChain
     * @throws CHttpException
     * @throws CException
     */
    public function filterValidateSuccessRequest(CFilterChain $filterChain)
    {
        $gateway = $this->createGateway();
        $data = implode('|', array($_GET['ORDER_NUMBER'], $_GET['TIMESTAMP'], $_GET['PAID'], $_GET['METHOD'], $gateway->apiSecret));
        if (!$this->validateAuthCode($_GET['RETURN_AUTHCODE'], $data)) {
            throw new CException('Invalid authentication code.');
        }
        $filterChain->run();
    }

    /**
     * @param CFilterChain $filterChain
     * @throws CHttpException
     * @throws CException
     */
    public function filterValidateFailureRequest(CFilterChain $filterChain)
    {
        $gateway = $this->createGateway();
        $data = implode('|', array($_GET['ORDER_NUMBER'], $_GET['TIMESTAMP'], $gateway->apiSecret));
        if (!$this->validateAuthCode($_GET['RETURN_AUTHCODE'], $data)) {
            throw new CException('Invalid authentication code.');
        }
        $filterChain->run();
    }

    /**
     * @return PaytrailGateway
     */
    protected function createGateway()
    {
        return $this->getPaymentManager()->createGateway('paytrail');
    }

    /**
     * @return bool
     */
    protected function validateAuthCode($code, $data)
    {
        return $code === strtoupper(md5($data));
    }

    /**
     * @return array access control rules.
     */
    public function accessRules()
    {
        return array(
            array('allow', 'users' => array('@')),
            array('deny'),
        );
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
        $this->redirect($manager->successUrl);
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
        $this->redirect($manager->failureUrl);
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
     * @param $orderNumber
     * @return PaymentTransaction
     * @throws CException
     */
    protected function loadTransaction($orderNumber)
    {
        $transaction = CActiveRecord::model($this->getPaymentManager()->transactionClass)->findByAttributes(
            array('orderIdentifier' => $orderNumber)
        );
        if ($transaction === null) {
            throw new CException(sprintf('Failed to load payment transaction with order identifier #%d.', $orderNumber));
        }
        return $transaction;
    }
}