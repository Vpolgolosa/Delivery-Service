<?php
namespace app\models;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Yii;
class Store extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'store';
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
            [['owner_id', 'store_name', 'store_address'], 'required'],
            [['owner_id'], 'integer'],
            [['store_name'], 'string', 'max' => 50],
            [['store_address'], 'string', 'max' => 100],
            [['owner_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::class, 'targetAttribute' => ['owner_id' => 'id']],
        ];
    }

    public function attributeLabels()
    {
        return [
            'store_id' => 'Store ID',
            'owner_id' => 'Owner ID',
            'store_name' => 'Store Name',
            'store_address' => 'Store Address',
        ];
    }

    public function getOrders()
    {
        return $this->hasMany(Order::class, ['storeid' => 'store_id']);
    }

    public function getOwner()
    {
        return $this->hasOne(User::class, ['id' => 'owner_id']);
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['store_id'],$fields['owner_id']);
        return $fields;
    }
}
