<?php
/**
 * PaytrailPayment class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2014
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-paytrail.models
 */

use NordSoftware\Paytrail\Object\Payment;

/**
 * This is the model class for table "paytrail_payment".
 *
 * The followings are the available columns in table 'paytrail_payment':
 * @property string $id
 * @property string $customerId
 * @property string $urlsetId
 * @property string $orderNumber
 * @property string $referenceNumber
 * @property string $description
 * @property string $currency
 * @property string $locale
 * @property boolean $includeVat
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property PaytrailContact $contact
 * @property PaytrailUrlset $urlset
 * @property PaytrailProduct[] $products
 */
class PaytrailPayment extends PaytrailActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'paytrail_payment';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('contactId, urlsetId, orderNumber', 'required'),
            array('includeVat, status', 'numerical', 'integerOnly' => true),
            array('currency', 'length', 'max' => 3),
            array('locale', 'length', 'max' => 5),
            array('contactId, urlsetId', 'length', 'max' => 10),
            array('referenceNumber', 'length', 'max' => 22),
            array('orderNumber', 'length', 'max' => 64),
            array('description', 'safe'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'contact' => array(self::BELONGS_TO, 'PaytrailContact', 'contactId'),
            'urlset' => array(self::BELONGS_TO, 'PaytrailUrlset', 'urlsetId'),
            'products' => array(
                self::MANY_MANY,
                'PaytrailProduct',
                'paytrail_payment_product(paymentId, productId)'
            ),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('paytrail', 'ID'),
            'contactId' => Yii::t('paytrail', 'Contact'),
            'urlsetId' => Yii::t('paytrail', 'Url set'),
            'orderNumber' => Yii::t('paytrail', 'Order Number'),
            'referenceNumber' => Yii::t('paytrail', 'Reference Number'),
            'description' => Yii::t('paytrail', 'Description'),
            'currency' => Yii::t('paytrail', 'Currency'),
            'locale' => Yii::t('paytrail', 'Locale'),
            'status' => Yii::t('paytrail', 'Status'),
        );
    }

    /**
     * @param PaytrailProduct $product
     */
    public function addProduct($product)
    {
        $model = new PaytrailPaymentProduct;
        $model->paymentId = $this->id;
        $model->productId = $product->id;
        if (!$model->save()) {
            throw new CException(sprintf(
                'Failed to link product #%d to paytrail payment #%d.',
                $product->id,
                $this->id
            ));
        }
        return $model;
    }

    public function toObject()
    {
        $object = new Payment;
        $object->configure(
            array(
                'orderNumber' => $this->orderNumber,
                'referenceNumber' => $this->referenceNumber,
                'description' => $this->description,
                'currency' => $this->currency,
                'locale' => $this->locale,
                'contact' => $this->contact->toObject(),
                'urlSet' => $this->urlset->toObject(),
            )
        );
        foreach ($this->products as $product) {
            $object->addProduct($product->toObject());
        }
        return $object;
    }

    /**
     * @param array $attributes
     * @return PaytrailPayment
     */
    public static function create(array $attributes)
    {
        $model = new PaytrailPayment;
        $model->attributes = $attributes;
        if (!$model->save()) {
            throw new CException('Failed to save paytrail payment.');
        }
        return $model;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PaytrailPayment the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
