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
    public $managerId = 'payment';

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

    public function actionFailure($transactionId)
    {
        $manager = $this->getPaymentManager();
        $transaction = $manager->loadTransaction($transactionId);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_FAILED, $transaction);
        die('failure');
    }

    public function actionNotify($transactionId)
    {
        //$transaction = $this->loadTransaction($transactionId);
    }

    public function actionPending($transactionId)
    {
        $manager = $this->getPaymentManager();
        $transaction = $manager->loadTransaction($transactionId);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_PENDING, $transaction);
        die('pending');
    }

    public function actionSuccess($transactionId)
    {
        $manager = $this->getPaymentManager();
        $transaction = $manager->loadTransaction($transactionId);
        $manager->changeTransactionStatus(PaymentTransaction::STATUS_COMPLETED, $transaction);
        die('success');
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