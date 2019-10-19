<?php

namespace app\controllers;

use app\models\IndexForm;
use DateTime;
use DateTimeZone;
use DOMDocument;
use DOMElement;
use DOMXPath;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new IndexForm();

        if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');
            if ($model->upload()) {

            }
        }
        /** @var array $result массив [время, значение] */
        $result = [];
        try {
            $domd = new DOMDocument();
            $domd->loadHTML('<?xml version="1.0" encoding="UTF-8"?>' . "\n" . file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/uploads/target.txt'));//<-- исходный файл отчета 
            $xpath = new DomXPath($domd);

            $dates = $xpath->query("//html/body/div/table/tr/td[2]");//<-- ячейки таблицы с датой

            /** @var DOMElement $date */
            foreach ($dates as $k => $date) {
                try {
                    $dateTime = DateTime::createFromFormat('Y.m.d H:i:s', $date->nodeValue);
                    if ($dateTime && $k > 5) {
                        $profit = $xpath->query("//html/body/div/table/tr[" . $k . "]/td[14]");
                        array_push($result, [$dateTime->getTimestamp() * 1000, (float)$profit[0]->nodeValue]);
                    }
                } catch (\Exception $e) {
                    // var_dump($e->getMessage());
                }
            }
        } catch (\Exception $e) {
            // var_dump($e->getMessage());
        }


        return $this->render('index', [
            'model' => $model,
            'result' => $result,
            'title' => $domd->getElementsByTagName('title')->item(0)->nodeValue,
        ]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }

        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
}
