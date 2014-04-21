<?php
Yii::import('application.modules.EmailBox.vendors.*');
require_once('AmazonSES.php');

class EmailBoxModule extends CWebModule
{
    protected static $_accessKey='YOUR_ACCESS_KEY';
    protected static $_secretKey='YOUR_SECRET_KEY';
    protected static $_validateEmail='YOUR_VALIDATE_EMAIL';
    public static  $limitEmailSendSecond=5;

    public function init()
    {
        // this method is called when the module is being created
        // you may place code here to customize the module or the application

        // import the module-level models and components
        $this->setImport(array(
            'EmailBox.models.*',
            'EmailBox.components.*',
        ));
    }

    public function beforeControllerAction($controller, $action)
    {
        if(parent::beforeControllerAction($controller, $action))
        {
            // this method is called before any module controller action is performed
            // you may place customized code here
            return true;
        }
        else
            return false;
    }

    public static function sendEmail($emailTo,$subject,$message)
    {
        $ses = new SimpleEmailService(
            self::$_accessKey,
            self::$_secretKey
        );

        $ses->verifyEmailAddress(self::$_validateEmail);

        $m = new SimpleEmailServiceMessage();
        $m->addTo($emailTo);
        $m->setFrom(self::$_validateEmail);
        $m->setSubject($subject);
        $m->setMessageFromString($message);

        $ses->sendEmail($m);
    }

    public static function addEmailInBD($priority,$emailTo,$subject,$message)
    {
        $EmailBox = new EmailBox;
        $EmailBox->priority=$priority;
        $EmailBox->emailTo=$emailTo;
        $EmailBox->subject=$subject;
        $EmailBox->html=$message;
        $EmailBox->text=$message;
        if ($EmailBox->save()) {
            return true;
        } else {
            return false;
        }
    }
    
    /**
     * Get email from DB and send message to email
     * Use Service AmasonSES
    **/
    public static function sendEmailFromBD()
    {
        $emailBox=EmailBox::model()->getAllEmailFromBD();

        foreach ($emailBox as $email) {
            $ses = new SimpleEmailService(
                self::$_accessKey,
                self::$_secretKey
            );

            $ses->verifyEmailAddress(self::$_validateEmail);

            $m = new SimpleEmailServiceMessage();
            $m->addTo($email->emailTo);
            $m->setFrom(self::$_validateEmail);
            $m->setSubject($email->subject);
            $m->setMessageFromString($email->html);

            if ($ses->sendEmail($m)) {
                $email->delete();
            };
        }

    }
}
