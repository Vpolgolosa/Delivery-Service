<?php
namespace app\controllers;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use function PHPUnit\Framework\returnArgument;
use app\models\Store;
use app\models\User;
class StoreController extends FunctionController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only'=>['create','edit','delete']
        ];
        return $behaviors;
    }
    public $modelClass = 'app\models\Store';

    public function actionCreate($id){
        $request=Yii::$app->request->post();
        $user=User::findOne($id);
        $usr=Yii::$app->user->identity;
        $stre=Store::findOne(['owner_id'=>$id]);
        if(isset($stre)) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Магазин уже создан']]);
        if(!$user) return $this->send(404,  ['content'=>['code'=>404, 'message'=>'Пользователь не найден']]);
        if(!$user->whois==0) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        if(!$usr->id==$id and !$usr->whois==1) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        $store=new Store($request);
        $store->owner_id=$id;
        if (!$store->validate()) return $this->validation($store);
        $store->save();
        return $this->send(200, "");
    }

    public function actionEdit($id)
    {
        $request=Yii::$app->request->getBodyParams();
        $user=User::findOne($id);
        if (!$user) return $this->send(404,  ['content'=>['code'=>404, 'message'=>'Пользователь не найден']]);
        $usr=Yii::$app->user->identity; // Получить идентифицированного пользователя
        if(!$user->whois==0) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        if(!$usr->id==$id and !$usr->whois==1) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        $store=Store::findOne(['owner_id'=>$id]);
        if(!$store) return $this->send(404,  ['content'=>['code'=>404, 'message'=>'Магазин не найден']]);
        if (isset($request['store_name'])) $store->store_name = $request['store_name'];
        if (isset($request['store_address'])) $store->store_address = $request['store_address'];
        if (!$store->validate()) return $this->validation($store);
        $store->save();
        return $this->send(200, "");
    }

    public function actionDelete($id)
    {
        $user=User::findOne($id);
        if (!$user) return $this->send(404,  ['content'=>['code'=>404, 'message'=>'Пользователь не найден']]);
        $usr=Yii::$app->user->identity; // Получить идентифицированного пользовате
        if(!$user->whois==0) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        if(!$usr->id==$id and !$usr->whois==1) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        $store=Store::findOne(['owner_id'=>$id]);
        if(!$store) return $this->send(404,  ['content'=>['code'=>404, 'message'=>'Магазин не найден']]);
        $store->delete();
        return $this->send(200, "");
    }
}