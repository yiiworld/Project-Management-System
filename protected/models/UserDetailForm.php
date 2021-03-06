<?php

/**
 * This is the model class for table "msuser".
 *
 * The followings are the available columns in table 'msuser':
 * @property integer $UserId
 * @property string $Username
 * @property string $Name
 * @property string $Email
 * @property string $Phone
 * @property string $Password
 * @property string $Enable
 */
class UserDetailForm extends CActiveRecord
{
	public $Confirm_Password;
	public $Copy_User;
	public $User;
	public $isChange = false;

	public function setUsername($Username){
		$this->Username = $Username;
	}
	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'msuser';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.				
		return array(
			array('Username, Name, Email, Phone, Password, Confirm_Password', 'required'),
			array('Username, Password, Confirm_Password', 'length', 'max'=>50),
			array('Name', 'length', 'max'=>250),
			array('Email', 'length', 'max'=>150),			
			array('Phone', 'length', 'max'=>20),			
			array('Enable', 'boolean'),
			array('Password','compare','compareAttribute'=>'Confirm_Password'),
			array('Email', 'email'),
			array('Username','unique','on'=>'insert'),
			array('Email','unique','on'=>'insert'),
			array('Email','unique','on'=>'update','criteria'=>array('condition'=>'`username`!=:Username','params'=>array(':Username'=>$this->Username))),			
			array('Phone', 'numerical','integerOnly'=>true),
			// The following rule is used by search().
			// @todo Please remove those attributes that should not be searched.
			array('UserId, Username, Name, Email, Phone, Password, Enable', 'safe', 'on'=>'search'),

		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return array(
			'UserId' => 'UserId',
			'Username' => 'Username',
			'Name' => 'Name',
			'Email' => 'Email',
			'Phone' => 'Phone',
			'Password' => 'Password',
			'Confirm_Password' => 'Confirm Password',
			'Copy_User' => 'Copy User',
			'Enable' => 'Enable',
			'User' => 'User',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 *
	 * Typical usecase:
	 * - Initialize the model fields with values from filter form.
	 * - Execute this method to get CActiveDataProvider instance which will filter
	 * models according to data in model fields.
	 * - Pass data provider to CGridView, CListView or any similar widget.
	 *
	 * @return CActiveDataProvider the data provider that can return the models
	 * based on the search/filter conditions.
	 */
	public function search()
	{
		// @todo Please modify the following code to remove attributes that should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('UserId',$this->UserId);
		$criteria->compare('Username',$this->Username,true);
		$criteria->compare('Name',$this->Name,true);
		$criteria->compare('Email',$this->Email,true);
		$criteria->compare('Phone',$this->Phone,true);
		$criteria->compare('Password',$this->Password,true);
		$criteria->compare('Enable',$this->Enable,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

	/**
	 * Returns the static model of the specified AR class.
	 * Please note that you should have this exact method in all your CActiveRecord descendants!
	 * @param string $className active record class name.
	 * @return UserDetailForm the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * Function for get single user detail, return 1 row
	 * @param string $UserId, user id.
	*/
	public function getUserDetail($UserId){
		$connection=Yii::app()->db;
		$connection->active=true;
	
		try
		{ 
			$sql = "call Spr_Get_UserDetail (".$UserId.")";
			$command=$connection->createCommand($sql);
			$dataReader=$command->query();
			$rows=$dataReader->readAll();
			return $rows;
		}
		catch(Exception $e)
		{
			$response = array('code'=>'', 'exception'=>'');
			$response['code'] = StandardVariable::CONSTANT_RETUNN_ERROR;
			$response['exception'] = $e->errorInfo;
			return $response;
		}
	}

	/**
	 * Function for get list user except parameter userid
	 * @param int $userid, userid
	*/
	public function getUserList($UserId){
		$connection=Yii::app()->db;
		$connection->active=true;
	
		try
		{ 
			$sql = "call Spr_Get_UserList('".$UserId."')";
			$command=$connection->createCommand($sql);
			$dataReader=$command->query();
			$rows=$dataReader->readAll();
			return $rows;
		}
		catch(Exception $e)
		{
			$response = array('code'=>'', 'exception'=>'');
			$response['code'] = StandardVariable::CONSTANT_RETUNN_ERROR;
			$response['exception'] = $e->errorInfo;
			return $response;
		}
	}

	/**
	 * Function for get copy list user
	 * @param int $userid, userid
	 * @param int $findByUserId, userid which want to find
	*/
	public function getCopyUserList($UserId, $findByUserId){
		$connection=Yii::app()->db;
		$connection->active=true;
	
		try
		{ 
			$sql = "call Spr_Get_Copy_UserList('".$UserId."',".$findByUserId.")";
			$command=$connection->createCommand($sql);
			$dataReader=$command->query();
			$rows=$dataReader->readAll();
			return $rows;
		}
		catch(Exception $e)
		{
			$response = array('code'=>'', 'exception'=>'');
			$response['code'] = StandardVariable::CONSTANT_RETUNN_ERROR;
			$response['exception'] = $e->errorInfo;
			return $response;
		}
	}

	/**
	 * Function for insert user detail
	 * @param string $Username, username.
	 * @param string $Name, name.
	 * @param string $Email, email.
	 * @param string $Phone, phone.
	 * @param string $Password, password
	 * @param boolean $Enable, enable/disable menu.
	 * @param string userAccess,  array of menu id which want assign to user.
	 * @param string userGroup,  array of group id which want assign to user.
	*/
	public function insertUser($Username, $Name, $Email, $Phone, $Password, $Enable, $isChange, $userAccess, $userGroup)
	{
		$response = array('code'=>'', 'exception'=>'');
		$connection=Yii::app()->db;
		$connection->active=true;
		$userin=GlobalFunction::getLoginUserName();
		
		$transaction=$connection->beginTransaction();
		try
		{ 
			$id = 0;
			if ($isChange == "1"){
				$sql = "call Spr_Insert_User ('".$Username."', '".$Name."', '".$Email."', 
					'".$Phone."','". CPasswordHelper::hashPassword($Password)."','".$Enable."', '".$userin."')";
				$command=$connection->createCommand($sql);				
				$dataReader=$command->query();
				$rows=$dataReader->readAll();
				$id = $rows[0]["UserId"];
				$dataReader->close();
			}
				
			$command = false;
			
			for($i = 0; $i < count($userAccess); $i++){
				if($userAccess[i] != 0){
					$sql = "call Spr_Insert_Update_User_Access (".$id.", ".$userAccess[$i].",'".$userin."')";
					$command=$connection->createCommand($sql);
					$status=$command->execute();
				}
			}
			
			for($i = 0; $i < count($userGroup); $i++){
				if($userGroup[i] != 0){
					$sql = "call Spr_Insert_Update_User_Group (".$id.", ".$userGroup[$i].",'".$userin."')";
					$command=$connection->createCommand($sql);
					$status=$command->execute();
				}
			}
			

		   	$transaction->commit();
		   	$response['code'] = StandardVariable::CONSTANT_RETURN_SUCCESS;
		}
		catch(Exception $e)
		{
			$response['code'] = StandardVariable::CONSTANT_RETUNN_ERROR;
			$response['exception'] = $e->errorInfo;			
		   	$transaction->rollback();
		}
		
		return $response;
	}

	/**
	 * Function for delete user
	 * @param int $UserId, user.
	*/
	public function deleteUser($UserId)
	{
		$response = array('code'=>'', 'exception'=>'');
		$connection=Yii::app()->db;
		$connection->active=true;
		$userin=GlobalFunction::getLoginUserName();
		
		$transaction=$connection->beginTransaction();
		try
		{ 
			$sql = "call Spr_Delete_User (".$UserId.", '".$userin."')";
			$command=$connection->createCommand($sql);
			$status=$command->execute();
		   	$transaction->commit();
		   	$response['code'] = StandardVariable::CONSTANT_RETURN_SUCCESS;
		}
		catch(Exception $e)
		{
			$response['code'] = StandardVariable::CONSTANT_RETUNN_ERROR;
			$response['exception'] = $e->errorInfo;			
		   	$transaction->rollback();
		}
		
		return $response;
	}

	/**
	 * Function for update user detail
	 * @param string $UserId, userid.
	 * @param string $Username, username.
	 * @param string $Name, name.
	 * @param string $Email, email.
	 * @param string $Phone, phone.
	 * @param string $Password, password
	 * @param boolean $Enable, enable/disable menu.
	 * @param string userAccess,  array of menu id which want assign to user.
	 * @param string userGroup,  array of group id which want assign to user.
	*/
	public function updateUser($UserId, $Username, $Name, $Email, $Phone, $Password, $Enable, $isChange, $userAccess, $userGroup)
	{
		$response = array('code'=>'', 'exception'=>'');
		$connection=Yii::app()->db;
		$connection->active=true;
		$userin=GlobalFunction::getLoginUserName();
		
		$transaction=$connection->beginTransaction();
		try
		{ 			
			if($Password == StandardVariable::CONSTANT_PASSWORD)
				$Password = 'NULL';
			else 
				$Password = "'".CPasswordHelper::hashPassword($Password)."'";

			if ($isChange == "1"){
				$sql = "call Spr_Update_User ('".$Username."', '".$Name."', '".$Email."', 
					'".$Phone."',".$Password.",'".$Enable."', '".$userin."')";
				$command=$connection->createCommand($sql);
				$status=$command->execute();
			}

			$command = false;
			$strParamAccess= "";

			for($i = 0; $i < count($userAccess); $i++){
				$strParamAccess = $strParamAccess.','.$userAccess[$i];
				if ($userAccess[$i] != 0){
					$sql = "call Spr_Insert_Update_User_Access (".$UserId.", ".$userAccess[$i].",'".$userin."')";
					$command=$connection->createCommand($sql);
					$status=$command->execute();
				}
			}
			if (count($userAccess) > 0){
				$strParamAccess = substr($strParamAccess, 1);
				$sql = "call Spr_Delete_User_Access (".$UserId.", '".$strParamAccess."', '".$userin."')";
				$command=$connection->createCommand($sql);
				$status=$command->execute();
			}
			
			$strParamUser = "";
			for($i = 0; $i < count($userGroup); $i++){
				$strParamUser = $strParamUser.','.$userGroup[$i];
				if($userGroup[$i] != 0){
					$sql = "call Spr_Insert_Update_User_Group (".$UserId.", ".$userGroup[$i].",'".$userin."')"; 
					$command=$connection->createCommand($sql);
					$status=$command->execute();
				}
			}
			
			if (count($userGroup) > 0){
				$strParamUser = substr($strParamUser, 1);
				$sql = "call Spr_Delete_User_Group (".$UserId.", '".$strParamUser."', '".$userin."')";
				$command=$connection->createCommand($sql);
				$status=$command->execute();
			}

		   	$transaction->commit();
		   	$response['code'] = StandardVariable::CONSTANT_RETURN_SUCCESS;
		}
		catch(Exception $e)
		{
			$response['code'] = StandardVariable::CONSTANT_RETUNN_ERROR;
			$response['exception'] = $e->errorInfo;			
		   	$transaction->rollback();
		}
		
		return $response;
	}
}

