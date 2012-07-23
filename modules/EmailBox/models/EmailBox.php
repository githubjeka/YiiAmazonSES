<?php

/**
 * This is the model class for table "emailBox".
 *
 * The followings are the available columns in table 'emailBox':
 * @property string $id
 * @property string $priority
 * @property string $emailTo
 * @property string $subject
 * @property string $text
 * @property string $html
 */
class EmailBox extends CActiveRecord
{
	/**
	 * Returns the static model of the specified AR class.
	 * @param string $className active record class name.
	 * @return EmailBox the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'emailBox';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('priority, emailTo, subject, text, html', 'required'),
			array('priority', 'length', 'max'=>1),
			array('emailTo', 'length', 'max'=>255),
			array('subject', 'length', 'max'=>100),
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, priority, emailTo, subject, text, html', 'safe', 'on'=>'search'),
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
			'id' => 'ID',
			'priority' => 'Priority',
			'emailTo' => 'Email To',
			'subject' => 'Subject',
			'text' => 'Text',
			'html' => 'Html',
		);
	}

	/**
	 * Retrieves a list of models based on the current search/filter conditions.
	 * @return CActiveDataProvider the data provider that can return the models based on the search/filter conditions.
	 */
	public function search()
	{
		// Warning: Please modify the following code to remove attributes that
		// should not be searched.

		$criteria=new CDbCriteria;

		$criteria->compare('id',$this->id,true);
		$criteria->compare('priority',$this->priority,true);
		$criteria->compare('emailTo',$this->emailTo,true);
		$criteria->compare('subject',$this->subject,true);
		$criteria->compare('text',$this->text,true);
		$criteria->compare('html',$this->html,true);

		return new CActiveDataProvider($this, array(
			'criteria'=>$criteria,
		));
	}

    public function getAllEmailFromBD($emailBox=array(),$priority=1)
    {
        if ($priority<=3) {
            $emailTemp=$this->getEmailOfPriority($priority);
            $emailBox=array_merge($emailBox,$emailTemp);
            $countEmailForSend=count($emailBox);

            if (EmailBoxModule::$limitEmailSendSecond<>$countEmailForSend){
               return self::getAllEmailFromBD($emailBox,$priority+1);
            };
        }
        return $emailBox;
    }

    protected function getEmailOfPriority ($priority)
    {
        $emailBox=EmailBox::model()->findAll(array(
            'condition'=>'priority='.$priority,
            'limit'=>EmailBoxModule::$limitEmailSendSecond,
        ));
        return $emailBox;
    }

}