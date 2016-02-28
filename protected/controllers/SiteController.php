<?php

/**
 * Class SiteController
 */
class SiteController extends Controller
{
    /**
     * Declares class-based actions.
     */
    public function actions()
    {
        return array(
            // captcha action renders the CAPTCHA image displayed on the contact page
            'captcha' => array(
                'class' => 'CCaptchaAction',
                'backColor' => 0xFFFFFF,
            ),
            // page action renders "static" pages stored under 'protected/views/site/pages'
            // They can be accessed via: index.php?r=site/page&view=FileName
            'page' => array(
                'class' => 'CViewAction',
            ),
        );
    }

    /**
     * This is the default 'index' action that is invoked
     * when an action is not explicitly requested by users.
     */
    public function actionIndex()
    {
        // renders the view file 'protected/views/site/index.php'
        // using the default layout 'protected/views/layouts/main.php'
        $this->render('index');
    }

    /**
     * This is the action to handle external exceptions.
     */
    public function actionError()
    {
        if ($error = Yii::app()->errorHandler->error) {
            if (Yii::app()->request->isAjaxRequest) {
                echo $error['message'];
            } else {
                $this->render('error', $error);
            }
        }
    }

    /**
     * Displays the contact page
     */
    public function actionContact()
    {
        $model = new ContactForm;
        if (isset($_POST['ContactForm'])) {
            $model->attributes = $_POST['ContactForm'];
            if ($model->validate()) {
                $name = '=?UTF-8?B?' . base64_encode($model->name) . '?=';
                $subject = '=?UTF-8?B?' . base64_encode($model->subject) . '?=';
                $headers = "From: $name <{$model->email}>\r\n" .
                    "Reply-To: {$model->email}\r\n" .
                    "MIME-Version: 1.0\r\n" .
                    "Content-Type: text/plain; charset=UTF-8";

                mail(Yii::app()->params['adminEmail'], $subject, $model->body, $headers);
                Yii::app()->user->setFlash('contact',
                    'Thank you for contacting us. We will respond to you as soon as possible.');
                $this->refresh();
            }
        }
        $this->render('contact', array('model' => $model));
    }

    /**
     * Displays the login page
     */
    public function actionLogin()
    {
        $model = new LoginForm;

        // if it is ajax validation request
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'login-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }

        // collect user input data
        if (isset($_POST['LoginForm'])) {
            $model->attributes = $_POST['LoginForm'];
            // validate user input and redirect to the previous page if valid
            if ($model->validate() && $model->login()) {
                $this->redirect(Yii::app()->user->returnUrl);
            }
        }
        // display the login form
        $this->render('login', array('model' => $model));
    }

    /**
     * Logs out the current user and redirect to homepage.
     */
    public function actionLogout()
    {
        Yii::app()->user->logout();
        $this->redirect(Yii::app()->homeUrl);
    }

    /**
     * @return CSqlDataProvider
     */
    public function actionWeather()
    {
        $model = new WeatherForm;
        // uncomment the following code to enable ajax-based validation
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'weather-form') {
            $data = $this->getDataFromDB();
            $response = $data->getData();
            echo json_encode(array('data' => $response[0]));

            Yii::app()->end();
        }

        if (isset($_POST['WeatherForm'])) {
            $model->attributes = $_POST['WeatherForm'];
            if ($model->validate()) {
                // form inputs are valid, do something here
                $this->actionWeatherData();
                Yii::app()->end();
            }
        }
        $this->render('weather', array('model' => $model));
    }

    /**
     * new page with DATA RESPONSE from DB/API
     */
    public function actionWeatherData()
    {
        $data = $this->getDataFromDB();
        $this->render('weatherData', array(
            'dataProvider' => $data,
        ));
    }

    /**
     * @return CSqlDataProvider
     */
    private function getDataFromDB()
    {

        $dataProvider = $this->getDataFromDataBase();
        if (!$dataProvider) {
            $result = $this->getFromAPI();
            $dataProvider = $this->getDataFromDataBase($result, true);
        }

        $lastUpdated = $dataProvider->getData();

        $utc = new DateTimeZone('UTC');
        $now = new DateTime('now', $utc);
        $today = $now->format('Y-m-d');

        $date1 = new DateTime($lastUpdated[0]['lastUpdated'], $utc);
        // calendar date
        $lastUpdatedDate = $date1->format('Y-m-d');
        // date + 5 minutes
        $date1->modify('+ 5 minute');

        // check if last update was more than one calendar day before
        if ($today > $lastUpdatedDate) {
            // insert new record
            $result = $this->getFromAPI();
            if (!$result) {
                return false;
            }

            $dataProvider = $this->getDataFromDataBase($result, true);
        } else {
            // check if 5 minutes passed after last update
            if ($date1 < $now) {
                // update existing record
                $result = $this->getFromAPI();
                if (!$result) {
                    return false;
                }
                $dataProvider = $this->getDataFromDataBase($result);
            }
        }

        return $dataProvider;
    }

    /**
     * @return bool|mixed
     */
    private function getFromAPI()
    {
        $curl = Yii::app()->curl;
        $curl->options['setOptions'][CURLOPT_HEADER] = false;
        $output = $curl->run('http://api.openweathermap.org/data/2.5/weather?q=London,uk&appid=44db6a862fba0b067b1930da0d769e98');

        if (!$output->hasErrors()) {
            echo Yii::trace(CVarDumper::dumpAsString('success'), 'vardump');
            return json_decode($output->getData());
        } else {
            echo Yii::trace(CVarDumper::dumpAsString($output->getErrors()), 'vardump');
        }

        return false;
    }

    /**
     * @param bool|false $api
     * @param bool|false $insert
     * @return bool|CSqlDataProvider
     */
    public function getDataFromDataBase($api = false, $insert = false)
    {
        if ($api) {
            $weatherDescription = $api->weather[0]->description;
            $temp = $api->main->temp;
            $humidity = $api->main->humidity;
            $lastUpdated = new DateTime('now', new DateTimeZone('UTC'));
            $lustUpdated = $lastUpdated->format('Y-m-d H:i:s');

            if (!$insert) {
                // update existing record
                $model = new WeatherActiveRecord;
                $criteria = new CDbCriteria;
                $criteria->select = 'max(id) AS id';
                $row = $model->model()->find($criteria);
                $post = WeatherActiveRecord::model()->findByPk($row['id']);
            } else {
                // create new record
                $post = new WeatherActiveRecord;
            }

            $post->description = $weatherDescription;
            $post->temp = $temp;
            $post->humidity = $humidity;
            $post->lastUpdated = $lustUpdated;
            $post->save();
        }

        $model = new WeatherForm;
        return $model::getDATA();
    }
}
