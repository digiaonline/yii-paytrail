<?php
/**
 * PaytrailContact class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2014
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-paytrail.models
 */

use NordSoftware\Paytrail\Object\Contact;

/**
 * This is the model class for table "paytrail_contact".
 *
 * The followings are the available columns in table 'paytrail_contact':
 * @property string $id
 * @property string $addressId
 * @property string $firstName
 * @property string $lastName
 * @property string $email
 * @property string $phoneNumber
 * @property string $mobileNumber
 * @property string $companyName
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property PaytrailAddress $address
 * @property PaytrailPayment[] $payments
 */
class PaytrailContact extends CActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'paytrail_contact';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('addressId, firstName, lastName, email', 'required'),
            array('status', 'numerical', 'integerOnly' => true),
            array('addressId', 'length', 'max' => 10),
            array('firstName, lastName, phoneNumber, mobileNumber', 'length', 'max' => 64),
            array('companyName', 'length', 'max' => 128),
            array('email', 'length', 'max' => 255),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'address' => array(self::BELONGS_TO, 'PaytrailAddress', 'addressId'),
            'payments' => array(self::HAS_MANY, 'PaytrailPayment', 'contactId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('paytrail', 'ID'),
            'addressId' => Yii::t('paytrail', 'Address'),
            'firstName' => Yii::t('paytrail', 'First Name'),
            'lastName' => Yii::t('paytrail', 'Last Name'),
            'email' => Yii::t('paytrail', 'Email'),
            'phoneNumber' => Yii::t('paytrail', 'Phone Number'),
            'mobileNumber' => Yii::t('paytrail', 'Mobile Number'),
            'companyName' => Yii::t('paytrail', 'Company Name'),
            'status' => Yii::t('paytrail', 'Status'),
        );
    }

    /**
     * @return Contact
     */
    public function toObject()
    {
        $object = new Contact;
        $object->configure(
            array(
                'firstName' => $this->firstName,
                'lastName' => $this->lastName,
                'email' => $this->email,
                'phoneNumber' => $this->phoneNumber,
                'mobileNumber' => $this->mobileNumber,
                'companyName' => $this->companyName,
                'address' => $this->address->toObject(),
            )
        );
        return $object;
    }

    /**
     * @param array $attributes
     * @return PaytrailContact
     */
    public static function create(array $attributes)
    {
        $model = new PaytrailContact;
        $model->attributes = $attributes;
        if (!$model->save()) {
            throw new CException('Failed to save paytrail contact.');
        }
        return $model;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PaytrailContact the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
