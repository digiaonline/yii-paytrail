<?php
/**
 * PaytrailAddress class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2014
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-paytrail.models
 */

use NordSoftware\Paytrail\Object\Address;

/**
 * This is the model class for table "paytrail_address".
 *
 * The followings are the available columns in table 'paytrail_address':
 * @property string $id
 * @property string $streetAddress
 * @property string $postalCode
 * @property string $postOffice
 * @property string $countryCode
 * @property integer $status
 */
class PaytrailAddress extends PaytrailActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'paytrail_address';
    }

    /**
     * @return array attached behaviors.
     */
    public function behaviors()
    {
        return array_merge(
            parent::behaviors(),
            array(
                'audit' => array(
                    'class' => 'AuditBehavior',
                ),
            )
        );
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('streetAddress, postalCode, postOffice, countryCode', 'required'),
            array('status', 'numerical', 'integerOnly' => true),
            array('countryCode', 'length', 'max' => 2),
            array('postalCode', 'length', 'max' => 16),
            array('postalCode, postOffice', 'length', 'max' => 64),
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
            'streetAddress' => Yii::t('paytrail', 'Street Address'),
            'postalCode' => Yii::t('paytrail', 'Postal Code'),
            'postOffice' => Yii::t('paytrail', 'Post Office'),
            'countryCode' => Yii::t('paytrail', 'Country Code'),
            'status' => Yii::t('paytrail', 'Status'),
        );
    }

    /**
     * @return Address
     */
    public function toObject()
    {
        $object = new Address;
        $object->configure(
            array(
                'streetAddress' => $this->streetAddress,
                'postalCode' => $this->postalCode,
                'postOffice' => $this->postOffice,
                'countryCode' => $this->countryCode,
            )
        );
        return $object;
    }

    /**
     * @param array $attributes
     * @return PaytrailAddress
     */
    public static function create(array $attributes)
    {
        $model = new PaytrailAddress;
        $model->attributes = $attributes;
        if (!$model->save()) {
            throw new CException('Failed to save paytrail address.');
        }
        return $model;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PaytrailAddress the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
