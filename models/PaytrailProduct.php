<?php
/**
 * PaytrailProduct class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2014
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-paytrail.models
 */

use NordSoftware\Paytrail\Object\Product;

/**
 * This is the model class for table "paytrail_product".
 *
 * The followings are the available columns in table 'paytrail_product':
 * @property string $id
 * @property string $title
 * @property string $code
 * @property string $quantity
 * @property string $price
 * @property string $vat
 * @property string $discount
 * @property string $type
 * @property integer $status
 *
 * The followings are the available model relations:
 */
class PaytrailProduct extends PaytrailActiveRecord
{
    const TYPE_NORMAL = 1;
    const TYPE_POSTAL = 2;
    const TYPE_HANDLING = 3;

    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'paytrail_product';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('title, quantity, price, vat, discount, type', 'required'),
            array('quantity, status', 'numerical', 'integerOnly' => true),
            array('vat, discount', 'length', 'max' => 5),
            array('quantity, type', 'length', 'max' => 10),
            array('price', 'length', 'max' => 15),
            array('code', 'length', 'max' => 16),
            array('title, code', 'length', 'max' => 255),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array();
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('paytrail', 'ID'),
            'title' => Yii::t('paytrail', 'Title'),
            'code' => Yii::t('paytrail', 'Code'),
            'quantity' => Yii::t('paytrail', 'Quantity'),
            'price' => Yii::t('paytrail', 'Price'),
            'vat' => Yii::t('paytrail', 'Vat'),
            'discount' => Yii::t('paytrail', 'Discount'),
            'type' => Yii::t('paytrail', 'Type'),
            'status' => Yii::t('paytrail', 'Status'),
        );
    }

    /**
     * @return Contact
     */
    public function toObject()
    {
        $object = new Product;
        $object->configure(
            array(
                'title' => $this->title,
                'code' => $this->code,
                'amount' => $this->quantity,
                'price' => $this->price,
                'vat' => $this->vat,
                'discount' => $this->discount,
                'type' => $this->type,
            )
        );
        return $object;
    }

    /**
     * @param array $attributes
     * @return PaytrailProduct
     */
    public static function create(array $attributes)
    {
        $model = new PaytrailProduct;
        $model->attributes = $attributes;
        if (!$model->save()) {
            throw new CException('Failed to save paytrail product.');
        }
        return $model;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PaytrailProduct the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
