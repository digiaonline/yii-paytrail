<?php
/**
 * PaytrailUrlset class file.
 * @author Christoffer Niska <christoffer.niska@nordsoftware.com>
 * @copyright Copyright &copy; Nord Software 2014
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @package nordsoftware.yii-paytrail.models
 */

use NordSoftware\Paytrail\Object\UrlSet;

/**
 * This is the model class for table "paytrail_urlset".
 *
 * The followings are the available columns in table 'paytrail_urlset':
 * @property string $id
 * @property string $successUrl
 * @property string $failureUrl
 * @property string $notificationUrl
 * @property string $pendingUrl
 * @property integer $status
 */
class PaytrailUrlset extends PaytrailActiveRecord
{
    /**
     * @return string the associated database table name
     */
    public function tableName()
    {
        return 'paytrail_urlset';
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
            array('successUrl, failureUrl, notificationUrl', 'required'),
            array('status', 'numerical', 'integerOnly' => true),
            array('pendingUrl', 'safe'),
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
            'successUrl' => Yii::t('paytrail', 'Success Url'),
            'failureUrl' => Yii::t('paytrail', 'Failure Url'),
            'notificationUrl' => Yii::t('paytrail', 'Notification Url'),
            'pendingUrl' => Yii::t('paytrail', 'Pending Url'),
            'status' => Yii::t('paytrail', 'Status'),
        );
    }

    /**
     * @return UrlSet
     */
    public function toObject()
    {
        $object = new UrlSet;
        $object->configure(
            array(
                'successUrl' => $this->successUrl,
                'failureUrl' => $this->failureUrl,
                'notificationUrl' => $this->notificationUrl,
                'pendingUrl' => $this->pendingUrl,
            )
        );
        return $object;
    }

    /**
     * @param array $attributes
     * @return PaytrailUrlset
     */
    public static function create(array $attributes)
    {
        $model = new PaytrailUrlset;
        $model->attributes = $attributes;
        if (!$model->save()) {
            throw new CException('Failed to save paytrail url set.');
        }
        return $model;
    }

    /**
     * Returns the static model of the specified AR class.
     * Please note that you should have this exact method in all your CActiveRecord descendants!
     * @param string $className active record class name.
     * @return PaytrailUrlset the static model class
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
