<?php
//Yii::import(Yii::app()->controller->module->vendorsPath);
//require_once(Yii::app()->controller->module->amazonSesPath);

class DefaultController extends Controller
{
	public function actionIndex()
	{
		$this->render('index');
	}
}