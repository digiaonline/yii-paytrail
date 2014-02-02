<?php
/**
 * PaytrailController class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2014
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-paytrail.controllers
 */

class PaytrailController extends CController
{
    /**
     * @var mixed
     */
    public $successRoute;

    /**
     * @var mixed
     */
    public $failureRoute;

    /**
     * @var string
     */
    public $managerId = 'payment';

    public function init()
    {
        if (!isset($this->successRoute)) {
            throw new CException('PaytrailController.successRoute must be set.');
        }
        if (!isset($this->failureRoute)) {
            throw new CException('PaytrailController.failureRoute must be set.');
        }
    }

    public function actionTest()
    {
        $contact = PaymentContact::create(
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

        $transaction = PaymentTransaction::create(
            array(
                'methodId' => 1,
                'shippingContactId' => $contact->id,
                'description' => 'Test payment',
                'price' => 100.00,
                'currency' => 'EUR',
                'vat' => 28.00,
            )
        );

        $transaction->addItem(
            array(
                'description' => 'Test product',
                'code' => '01234',
                'quantity' => 1,
                'price' => 19.90,
                'vat' => 23.00,
                'discount' => 5.00,
                'type' => 1,
            )
        );

        Yii::app()->payment->pay(1, $transaction);
    }

    /**
     * @param int $transactionId
     */
    public function actionSuccess($transactionId)
    {
        $manager = $this->getPaymentManager();
        $transaction = $manager->loadTransaction($transactionId);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_COMPLETED, $transaction);
        $this->redirect($this->successRoute);
    }

    /**
     * @param int $transactionId
     */
    public function actionFailure($transactionId)
    {
        $manager = $this->getPaymentManager();
        $transaction = $manager->loadTransaction($transactionId);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_FAILED, $transaction);
        $this->redirect($this->failureRoute);
    }

    /**
     * @param $transactionId
     */
    public function actionNotify($transactionId)
    {
    }

    /**
     * @param int $transactionId
     */
    public function actionPending($transactionId)
    {
        $manager = $this->getPaymentManager();
        $transaction = $manager->loadTransaction($transactionId);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_PENDING, $transaction);
    }

    /**
     * @return PaymentManager
     * @throws CException
     */
    protected function getPaymentManager()
    {
        if (!Yii::app()->hasComponent($this->managerId)) {
            throw new CException(sprintf('PaytrailController.managerId is invalid.'));
        }
        return Yii::app()->getComponent($this->managerId);
    }
}