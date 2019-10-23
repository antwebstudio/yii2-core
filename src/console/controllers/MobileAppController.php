<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Console;

/**
 * @author Eugene Terentev <eugene@terentev.net>
 */
class MobileAppController extends Controller
{
    public function actionTest() {

        $apiKey = 'AAAArHUOJPs:APA91bE9aquNnH98qG8CW_owM5BfXRkt459GC3EucvaBYiItdkvT6ul3f-wX6PdRfigcZQEJRvJIkiOZh7G7_liwdtSb6dYz7uMJQMKOmLPYvTx9RMk5uDgfdpJav-3yBWANRje9m7Hwa935l6a9aPeng2D2ozjd6A';
        $apiKey = 'AIzaSyC5eQ8ldFatHRONT9UzIJTcLd5JMgrmtzM';
        //$apiKey = 'AIzaSyC5eQ8ldFatHRONT9UzIJTcLd5JMgrmtzM';
        //$apiKey = 'AIzaSyAc_PTkIgCXX-I8YtNGRROBZmvux3qOwuA';
        $client = new Client(['verify' => false]);
        $client->setApiKey($apiKey);
        $client->injectHttpClient(new \GuzzleHttp\Client(['verify' => false]));

        $note = new Notification('test title', 'testing body');
        $note->setIcon('notification_icon_resource_name')
            ->setColor('#ffffff')
            ->setBadge(1);

        $regId = 'el3e3q6OV30:APA91bEK-TaCB-Lob84Lq6oK-nqiPW3Cg5-9BYlQI6zuo43_k18-F9tq60IVHhBrVRhHhLG2r1Bs9JRYreXaH2A6aNHGlDcc3NZNMmvXzTf_H-eoeWHngfe32QS9iPhuwv7-aQIh2m4S';
        $regId = '740698236155';
        //$regId = '1:740698236155:android:f2af3ffd681646fe';

        $message = new Message();
        $message->addRecipient(new Device($regId));
        $message->setNotification($note)
        ->setData(['title' => 'test', 'message' => 'test']);
            //->setData(['data' => ['title' => 'test', 'message' => 'test']]);

        $response = $client->send($message);
        var_dump($response->getStatusCode());
/*
        $appId = 'chy1988';
        $pusher = new AndroidPusher($appId);
        $regId = 'el3e3q6OV30:APA91bEK-TaCB-Lob84Lq6oK-nqiPW3Cg5-9BYlQI6zuo43_k18-F9tq60IVHhBrVRhHhLG2r1Bs9JRYreXaH2A6aNHGlDcc3NZNMmvXzTf_H-eoeWHngfe32QS9iPhuwv7-aQIh2m4S';
        $data = ['aps' => ['alert' => 'test']];
        $data = ['title' => 'test', 'message' => 'test message'];
        $pusher->notify($regId, $data);
        print_r($pusher->getOutputAsArray());
        echo 'Done';*/
    }
}
