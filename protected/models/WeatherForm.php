<?php

/**
 * WeatherForm class.
 * WeatherForm is the post request
 * WeatherForm form data. It is used by the 'weather' action of 'SiteController'.
 */
class WeatherForm extends CFormModel
{
    public $verifyCode;

    /**
     * Declares the validation rules.
     */
    public function rules()
    {
        return array(
            // verifyCode needs to be entered correctly
            array('verifyCode', 'captcha', 'allowEmpty' => !CCaptcha::checkRequirements()),
        );
    }

    /**
     * Declares customized attribute labels.
     * If not declared here, an attribute would have a label that is
     * the same as its name with the first letter in upper case.
     */
    public function attributeLabels()
    {
        return array(
            'verifyCode' => 'Verification Code',
        );
    }

    /**
     * @return bool|CSqlDataProvider
     */
    public static function getDATA()
    {
        $count = Yii::app()->db->createCommand("SELECT * FROM weather ORDER By id DESC LIMIT 1")->queryScalar();
        if ($count < 1) {
            return false;
        }

        $sql = "SELECT * FROM weather ORDER By id DESC";
        $dataProvider = new CSqlDataProvider($sql, array(
            'totalItemCount' => 1,
            'sort' => array(
                'attributes' => array(
                    'id',
                    'description',
                    'temp',
                    'humidity',
                    'lastUpdated',
                ),
            ),
            'pagination' => array(
                'pageSize' => 1,
            ),
        ));

        return $dataProvider;
    }
}
