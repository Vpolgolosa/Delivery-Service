<?php
namespace app\controllers;
use Yii;
use app\models\LoginForm;
use app\models\User;
use yii\web\UploadedFile;
use yii\filters\auth\HttpBearerAuth;
use function PHPUnit\Framework\returnArgument;
class UserController extends FunctionController
{
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'only'=>['create_courier','edit','delete']
        ];
        return $behaviors;
    }
    public $modelClass = 'app\models\User';

    public function actionCreate(){
        $request=Yii::$app->request->post();
        $user=new User($request);
        if (!$user->validate()) return $this->validation($user);
        $user->password=Yii::$app->getSecurity()->generatePasswordHash($user->password);
        $user->save();
        return $this->send(204, "");
    }

    public function actionLogin(){
        $request=Yii::$app->request->post();
        $loginForm=new LoginForm($request);
        if (!$loginForm->validate()) return $this->validation($loginForm);
        $user=User::find()->where(['email'=>$request['email']])->one();
        if (isset($user) && Yii::$app->getSecurity()->validatePassword($request['password'], $user->password)){
            $user->token=Yii::$app->getSecurity()->generateRandomString();
            $user->save(false);
            return $this->send(200, ['content'=>['token'=>$user->token]]);
        }
        return $this->send(401, ['content'=>['code'=>401, 'message'=>'Неверный email или пароль']]);
    }

    public function actionCreate_courier(){
        $usr=Yii::$app->user->identity;
        if(!$usr->whois==1) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        $request=Yii::$app->request->post();
        $user=new User($request);
        if (!$user->validate()) return $this->validation($user);
        $user->password=Yii::$app->getSecurity()->generatePasswordHash($user->password);
        $user->save();
        return $this->send(204, "");
    }

    public function actionEdit($id)
    {
        $request=Yii::$app->request->getBodyParams();
        $user=User::findOne($id);
        if (!$user) return $this->send(404,  ['content'=>['code'=>404, 'message'=>'Пользователь не найден']]);
        $user=Yii::$app->user->identity; // Получить идентифицированного пользователя
        if (isset($request['email'])) $user->login = $request['email'];
        if (isset($request['password'])) $user->password = $request['password'] = Yii::$app->getSecurity()->generatePasswordHash($user->password);
        if (isset($request['first_name'])) $user->first_name = $request['first_name'];
        if (isset($request['last_name'])) $user->last_name = $request['last_name'];
        if (isset($request['phone'])) $user->phone = $request['phone'];
        $uploadedFile = UploadedFile::getInstanceByName('profile_photo');
        if (isset($uploadedFile)){
            $user->profile_photo = UploadedFile::getInstanceByName('profile_photo');
            $hash=hash('sha256', $user->profile_photo->baseName) . '.' . $user->profile_photo->extension;
            $user->profile_photo->saveAs(\Yii::$app->basePath . '/assets/upload/' . $hash);
            $user->profile_photo=$hash;
        }
        if ($user->whois==3 and isset($request['address'])) $user->address = $request['address'];

        if (!$user->validate()) return $this->validation($user);
        $user->save();
        return $this->send(200, ['content'=>['status'=>'ok']]);
    }

    public function actionDelete($id)
    {
        $usr=Yii::$app->user->identity;
        if(!$usr->whois==1) return $this->send(403, ['content'=>['code'=>403, 'message'=>'Доступ запрещен']]);
        $user = User::findOne($id);
        if(!$user) return $this->send(404,  ['content'=>['code'=>404, 'message'=>'Пользователь не найден']]);
        $user->delete();
        return $this->send(200, "");
    }

}