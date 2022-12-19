<?php
namespace app\controllers;
use yii\rest\Controller;
use yii\web\Response;
use yii\widgets\ActiveForm;
class FunctionController extends Controller{

    public function send($code, $data){
        $response=$this->response;
        $response->format = Response::FORMAT_JSON;
        header('Access-Control-Allow-Origin: *');
        $response->data=$data;
        $response->statusCode=$code;
        return $response;
    }
    public function validation($model){
        $error=['error'=> ['code'=>422, 'message'=>'Validation error',
            'errors'=>ActiveForm::validate($model)]];
        return $this->send(422, $error);
    }
}