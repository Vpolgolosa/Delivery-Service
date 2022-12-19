<?php
namespace app\controllers;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use function PHPUnit\Framework\returnArgument;
use app\models\Store;
use app\models\User;
use app\models\Order;
use yii\db\Query;
class OrderController extends FunctionController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only'=>['create','view_all','take', 'edit', 'view_my', 'view']
        ];
        return $behaviors;
    }
    public $modelClass = 'app\models\Order';

    public function actionCreate($id){
        $request=Yii::$app->request->post();
        $configclient=[
            'first_name'=>$request['client_first_name'],
            'last_name'=>$request['client_last_name'],
            'phone'=>$request['client_phone'],
            'email'=>$request['client_email'],
            'address'=>$request['client_address']
        ];
        $order_class=NULL;
        switch ($request['order_class']){
            case 'light':
                $order_class=1;
                break;
            case 'medium':
                $order_class=2;
                break;
            case 'heavy':
                $order_class=3;
                break;
        }
        if($order_class==NULL)return $this->send(422, ['content'=>['code'=>422, 'message'=>'Класс заказа указан неверно']]);
        $configorder=[
            'order_class'=>$request['order_class']
        ];

        $store=Store::findOne($id);
        $usr=Yii::$app->user->identity;
        if(!$store) return $this->send(404,  ['content'=>['code'=>404, 'message'=>'Магазин не найден']]);
        if(!$usr->whois==0 and !$usr->whois==1) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        if(!$usr->id==$store->owner_id) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        $user=User::findOne(['email'=>$request['client_email']]);
        $password=NULL;
        if (!$user){
            $user=new User($configclient);
            $length = 10;
            $chars = array_merge(range(0,9), range('a','z'), range('A','Z'));
            shuffle($chars);
            $password = implode(array_slice($chars, 0, $length));
            $user->password=Yii::$app->getSecurity()->generatePasswordHash($password);
            $user->whois=3;
            if (!$user->validate()) return $this->validation($user);
            $user->save();
            $user=User::findOne(['email'=>$request['client_email']]);
        }
        $order=new Order($configorder);
        $order->storeid=$id;
        $order->client_id=$user->id;
        if (!$order->validate()) return $this->validation($order);
        $order->save();
        return $this->send(200, ['content'=>['client_password'=>$password]]);
    }

    public function actionView_all()
    {
        $usr=Yii::$app->user->identity;
        if(!$usr->whois==2 and !$usr->whois==1) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        $request=Yii::$app->request->get();
        $order_class=NULL;
        switch ($request['order_class']){
            case 'light':
                $order_class=1;
                break;
            case 'medium':
                $order_class=2;
                break;
            case 'heavy':
                $order_class=3;
                break;
        }
        if($order_class==NULL)return $this->send(422, ['content'=>['code'=>422, 'message'=>'Класс заказа указан неверно']]);
        $orders = (new Query())
            -> select(['store_address','address','order_date','order_status'])
            -> from(['order'])
            -> join('INNER JOIN', 'user', 'id = client_id')
            -> join('INNER JOIN', 'store', 'store_id = storeid')
            -> where(['order_class'=>$request['order_class']])
            -> indexBy('order_id')
            -> all();
        return $this->send(200, ['content'=> ['Заказы'=>$orders]]);
    }

    public function actionTake($id)
    {
        $usr=Yii::$app->user->identity;
        if(!$usr->whois==2 and !$usr->whois==1) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        if(!$usr->id==$id) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        $user=User::findOne($id);
        if(!$user)return $this->send(404, ['content'=>['code'=>404, 'message'=>'Пользователь не найден']]);
        if(!$user->whois==2) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Пользователь не является курьером']]);
        $request=Yii::$app->request->getBodyParams();
        if (isset($request['order_id'])) {
            $order=Order::findOne($request['order_id']);
            if(!$order)return $this->send(404, ['content'=>['code'=>404, 'message'=>'Заказ не найден']]);
            if(isset($order->courier_id))return $this->send(403, ['content'=>['code'=>403, 'message'=>'Заказ уже принят другим курьером']]);
            $order->courier_id = $id;
            $order->order_status = 1;
            if (!$order->validate()) return $this->validation($order);
            $order->save();
            return $this->send(200, "");
        }
        return $this->send(422, ['content'=>['code'=>422, 'message'=>'Заказ не указан']]);
    }

    public function actionEdit($id)
    {
        $usr=Yii::$app->user->identity;
        if(!$usr->whois==2 and !$usr->whois==1) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        if(!$usr->id==$id) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        $user=User::findOne($id);
        if(!$user)return $this->send(404, ['content'=>['code'=>404, 'message'=>'Пользователь не найден']]);
        if(!$user->whois==2) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Пользователь не является курьером']]);
        $request=Yii::$app->request->getBodyParams();
        if (isset($request['order_id'])) {
            $order=Order::findOne($request['order_id']);
            if(!$order)return $this->send(404, ['content'=>['code'=>404, 'message'=>'Заказ не найден']]);
            if(!$order->courier_id==$id)return $this->send(403, ['content'=>['code'=>403, 'message'=>'Заказ принят другим курьером']]);
            if(isset($request['order_status'])){
                $status=NULL;
                switch ($request['order_status']){
                    case 'взят':
                        $status=1;
                        break;
                    case 'в пути':
                        $status=2;
                        break;
                    case 'доставлен':
                        $status=3;
                        break;
                }
                if($status==NULL)return $this->send(422, ['content'=>['code'=>422, 'message'=>'Статус заказа указан неверно']]);
                if($order->order_status==$status)return $this->send(403, ['content'=>['code'=>403, 'message'=>'Заказ уже имеет этот статус']]);
                $order->order_status = $status;
                if (!$order->validate()) return $this->validation($order);
                $order->save();
                return $this->send(200, "");
            }
        }
        return $this->send(422, ['content'=>['code'=>422, 'message'=>'Заказ не указан']]);
    }

    public function actionView_my($id)
    {
        $usr=Yii::$app->user->identity;
        if(!$usr->whois==3 and !$usr->whois==1) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        $user=User::findOne($id);
        if(!$user)return $this->send(404, ['content'=>['code'=>404, 'message'=>'Пользователь не найден']]);
        if(!$user->whois==3) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        if(!$usr->id==$id)return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        $orders = (new Query())
            -> select(['order_status'])
            -> from(['order'])
            -> where(['client_id'=>$id])
            -> indexBy('order_id')
            -> all();
        return $this->send(200, ['content'=> ['Заказы'=>$orders]]);
    }

    public function actionView($id)
    {
        $usr=Yii::$app->user->identity;
        if(!$usr->whois==0 and !$usr->whois==1) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        $store=Store::findOne($id);
        if(!$store)return $this->send(404, ['content'=>['code'=>404, 'message'=>'Магазин не найден']]);
        if(!$usr->id==$store->owner_id)return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        $orders = (new Query())
            -> select(['order_date','order_status'])
            -> from(['order'])
            -> where(['storeid'=>$id])
            -> indexBy('order_id')
            -> all();
        return $this->send(200, ['content'=> ['Заказы'=>$orders]]);
    }
}