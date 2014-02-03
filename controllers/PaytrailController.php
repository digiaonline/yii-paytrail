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
            'validateRequest + success, failure, notify, pending',
        );
    }

    /**
     * @param CFilterChain $filterChain
     * @throws CHttpException
     * @throws CException
     */
    public function filterValidateRequest(CFilterChain $filterChain)
    {
        if (!isset($_GET['ORDER_NUMBER']) || !isset($_GET['TIMESTAMP']) || !isset($_GET['PAID']) || !isset($_GET['METHOD'])) {
            throw new CHttpException(400, 'Invalid request.');
        }
        if (!$this->validateAuthCode()) {
            throw new CException('Invalid authentication code.');
        }
        $filterChain->run();
    }

    /**
     * @return bool
     */
    protected function validateAuthCode()
    {
        $manager = $this->getPaymentManager();
        /** @var PaytrailGateway $gateway */
        $gateway = $manager->createGateway('paytrail');
        $data = implode(
            '|',
            array(
                $_GET['ORDER_NUMBER'],
                $_GET['TIMESTAMP'],
                $_GET['PAID'],
                $_GET['METHOD'],
                $gateway->apiSecret,
            )
        );
        return $_GET['RETURN_AUTHCODE'] === strtoupper(md5($data));
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

    public function actionTest()
    {
        $transaction = PaymentTransaction::create(
            array(
                'gateway' => 'paytrail',
                'orderIdentifier' => 1,
                'description' => 'Test payment',
                'price' => 100.00,
                'currency' => 'EUR',
                'vat' => 28.00,
            )
        );

        $transaction->addShippingContact(
            array(
                'firstName' => 'Foo',
                'lastName' => 'Bar',
                'email' => 'foo@bar.com',
                'phoneNumber' => '1234567890',
                'mobileNumber' => '0400123123',
                'companyName' => 'Test company',
                'streetAddress' => 'Test street 1',
                'postalCode' => '12345',
                'postOffice' => 'Helsinki',
                'countryCode' => 'FIN',
            )
        );

        $transaction->addItem(
            array(
                'description' => 'Test product',
                'code' => '01234',
                'quantity' => 5,
                'price' => 19.90,
                'vat' => 23.00,
                'discount' => 10.00,
                'type' => 1,
            )
        );

        $transaction->addItem(
            array(
                'description' => 'Another test product',
                'code' => '43210',
                'quantity' => 1,
                'price' => 49.90,
                'vat' => 23.00,
                'discount' => 50.00,
                'type' => 1,
            )
        );

        Yii::app()->payment->startTransaction($transaction);
    }

    /**
     * @param int $transactionId
     */
    public function actionSuccess($transactionId)
    {
        $manager = $this->getPaymentManager();
        $transaction = $manager->loadTransaction($transactionId);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_SUCCESSFUL, $transaction);
        $this->redirect($manager->successUrl);
    }

    /**
     * @param int $transactionId
     */
    public function actionFailure($transactionId)
    {
        $manager = $this->getPaymentManager();
        $transaction = $manager->loadTransaction($transactionId);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_FAILED, $transaction);
        $this->redirect($manager->failureUrl);
    }

    /**
     * @param int $transactionId
     */
    public function actionNotify($transactionId)
    {
        $manager = $this->getPaymentManager();
        $transaction = $manager->loadTransaction($transactionId);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_COMPLETED, $transaction);
        Yii::app()->end();
    }

    /**
     * @param int $transactionId
     */
    public function actionPending($transactionId)
    {
        $manager = $this->getPaymentManager();
        $transaction = $manager->loadTransaction($transactionId);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_PENDING, $transaction);
        Yii::app()->end();
    }
}