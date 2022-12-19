<?php
namespace app\models;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Yii;
class Order extends ActiveRecord implements IdentityInterface
{

    public static function tableName()
    {
        return 'order';
    }

    public static function findIdentity($id)
    {
        return static::findOne($id);
    }
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email]);
    }
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getAuthKey()
    {
        return ;
    }
    public function validateAuthKey($authKey)
    {
        return ;
    }
    public function validatePassword($password)
    {
        $hash = Yii::$app->getSecurity()->generatePasswordHash($password);
        if (Yii::$app->getSecurity()->validatePassword($password, $hash)) {
            return $this;
        }
        else {
            return 0;
        }
    }

    public function rules()
    {
        return [
            [['storeid', 'client_id', 'order_class'], 'required'],
            [['storeid', 'courier_id', 'client_id', 'order_status'], 'integer'],
            [['order_date'], 'safe'],
            [['order_class'], 'string', 'max' => 20],
            [['storeid'], 'exist', 'skipOnError' => true, 'targetClass' => Store::class, 'targetAttribute' => ['storeid' => 'store_id']],
            [['courier_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['courier_id' => 'id']],
            [['client_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['client_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'order_id' => 'Order ID',
            'storeid' => 'Storeid',
            'courier_id' => 'Courier ID',
            'client_id' => 'Client ID',
            'order_class' => 'Order Class',
            'order_date' => 'Order Date',
            'order_status' => 'Order Status',
        ];
    }

    public function getClient()
    {
        return $this->hasOne(User::class, ['id' => 'client_id']);
    }

    public function getCourier()
    {
        return $this->hasOne(User::class, ['id' => 'courier_id']);
    }

    public function getStore()
    {
        return $this->hasOne(Store::class, ['store_id' => 'storeid']);
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['order_id'],$fields['storeid'],$fields['courier_id'],$fields['client_id']);
        return $fields;
    }
}
