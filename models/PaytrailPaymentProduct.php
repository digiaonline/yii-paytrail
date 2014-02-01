<?php
/**
 * PaytrailPaymentProduct class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2014
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-paytrail.models
 */

/**
 * This is the model class for table "paytrail_payment_product".
 *
 * The followings are the available columns in table 'paytrail_payment_product':
 * @property string $id
 * @property string $paymentId
 * @property string $productId
 *
 * The followings are the available model relations:
 * @property PaytrailProduct $product
 * @property PaytrailPayment $payment
 */
class PaytrailPaymentProduct extends CActiveRecord
{
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'paytrail_payment_product';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return array(
			array('paymentId, productId', 'required'),
			array('paymentId, productId', 'length', 'max'=>10),
		);
	}
}
