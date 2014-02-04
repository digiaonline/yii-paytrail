<?php
/**
 * PaytrailResult class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2014
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-paytrail.models
 */

/**
 * This is the model class for table "paytrail_result".
 *
 * The followings are the available columns in table 'paytrail_result':
 * @property string $id
 * @property string $paymentId
 * @property string $orderNumber
 * @property string $token
 * @property string $url
 * @property integer $status
 *
 * The followings are the available model relations:
 * @property PaytrailPayment $payment
 */
class PaytrailResult extends PaytrailActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'paytrail_result';
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
            array('paymentId, token, url', 'required'),
            array('status', 'numerical', 'integerOnly' => true),
            array('paymentId', 'length', 'max' => 10),
            array('orderNumber, token', 'length', 'max' => 64),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'payment' => array(self::BELONGS_TO, 'PaytrailPayment', 'paymentId'),
        );
    }

    /**
     * @return array customized attribute labels (name=>label)
     */
    public function attributeLabels()
    {
        return array(
            'id' => Yii::t('paytrail', 'ID'),
            'paymentId' => Yii::t('paytrail', 'Payment'),
            'orderNumber' => Yii::t('paytrail', 'Order number'),
            'token' => Yii::t('paytrail', 'Token'),
            'url' => Yii::t('paytrail', 'Url'),
            'status' => Yii::t('paytrail', 'Status'),
        );
    }

    /**
     * @param array $attributes
     * @return PaytrailResult
     */
    public static function create(array $attributes)
    {
        $model = new PaytrailResult;
        $model->attributes = $attributes;
        if (!$model->save()) {
            throw new CException('Failed to save paytrail result.');
        }
        return $model;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PaytrailResult the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
