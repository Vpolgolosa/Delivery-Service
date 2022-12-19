<?php
namespace app\models;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;
use Yii;
class User extends ActiveRecord implements IdentityInterface
{
    public static function tableName()
    {
        return 'user';
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
            [['first_name', 'last_name', 'phone', 'email', 'password'], 'required'],
            [['profile_photo'], 'string'],
            [['whois'], 'integer'],
            [['first_name', 'last_name'], 'string', 'max' => 30],
            [['phone'], 'string', 'max' => 12],
            [['email'], 'string', 'max' => 50],
            [['password', 'address', 'token'], 'string', 'max' => 100],
        ];
    }

    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'first_name' => 'First Name',
            'last_name' => 'Last Name',
            'phone' => 'Phone',
            'email' => 'Email',
            'password' => 'Password',
            'profile_photo' => 'Profile Photo',
            'address' => 'Address',
            'token' => 'Token',
            'whois' => 'Whois',
        ];
    }

    public function getOrders()
    {
        return $this->hasMany(Order::class, ['courier_id' => 'id']);
    }

    public function getOrders0()
    {
        return $this->hasMany(Order::class, ['client_id' => 'id']);
    }

    public function getStores()
    {
        return $this->hasMany(Store::class, ['owner_id' => 'id']);
    }

    public function fields()
    {
        $fields = parent::fields();
        unset($fields['password'],$fields['id'], $fields['token']);
        return $fields;
    }
}
