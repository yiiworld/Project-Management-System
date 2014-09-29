<?php

class UserController extends Controller
{
	public $layout='//layouts/master';
	
	/**
	 * This is the default 'index' action that is invoked
	 * when an action is not explicitly requested by users.
	 */
	public function actionIndex()
	{
		$this->actionManageuser();
	}

	public function actionManageuser()
	{
		$model=new UserDetailForm;
		$model->Enable=1;
		if(isset($_POST['ajax']) && $_POST['ajax']==='user-detail-form-userdetail-form')
		{
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
		
		// user detail form POST
		if(isset($_POST['UserDetailForm']))
		{
			$model->attributes=$_POST['UserDetailForm'];
			if($model->validate())
			{
				$model->Username = $_POST['UserDetailForm']['Username'];
				$model->Name = $_POST['UserDetailForm']['Name'];
				$model->Email = $_POST['UserDetailForm']['Email'];
				$model->Phone = $_POST['UserDetailForm']['Phone'];
				$model->Password = $_POST['UserDetailForm']['Password'];				
				$model->Enable = $_POST['UserDetailForm']['Enable'];
				$model->User = $_POST['User'];

				

				$response = $model->getUserDetail($model->UserId);
				if ($model->UserId != null && $model->UserId != ""){
					if (!empty($response) && isset($response['UserId'])){
						
						$response1 = $model->updateUser($model->Username, $model->Name, $model->Email, $model->Phone, $model->Password, $model->Enable);				
						if ($response1['code'] == StandardVariable::CONSTANT_RETURN_SUCCESS){
							$this->redirect(array('um/user'));
						}else{
							$model->addError('request', $response1['exception'][2]);
						}
					}
					else{
						$model->addError('request', 'User not exists');
					}
				}
				else{
					$response1 = $model->insertUser($model->Username, $model->Name, $model->Email, $model->Phone, $model->Password, $model->Enable);
					if ($response1['code'] == StandardVariable::CONSTANT_RETURN_SUCCESS){
						$this->redirect(array('um/user'));
					}else{
						$model->addError('request', $response1['exception'][2]);
					}
				}
			}
		}else{
			$paramURL = CHttpRequest::getParam('id');
			if (isset($paramURL) && $paramURL != null && $paramURL != ""){
				$response = $model->getUserDetail($paramURL);
				if (!empty($response) && isset($response[0]['UserId'])){
					  $model->Username = $response[0]['Username'];
					  $model->Name = $response[0]['Name'];
					  $model->Email = $response[0]['Email'];
					  $model->Phone = $response[0]['Phone'];
					  $model->Password = $response[0]['Password'];
					  $model->Enable = $response[0]['Enable'];
				  }
			}	
		}

		$userlist = $model->getUserListDDL();
		$this->render('manageuser', array('model'=>$model, 'data'=>$userlist));
	}
}