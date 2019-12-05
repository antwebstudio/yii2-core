<?php
namespace ant\widgets;

use yii\helpers\Html;

class CountDown extends \yii\base\Widget {
    const DATA_DATE = 'data-countdown-date';

    public $datetime;
    public $template = '%M:%S';
    public $alertBefore = 60 ;
    public $alert;
    public $finish;
    public $update;

    public function init() {
        \ant\widgets\assets\CountDownAsset::register($this->view);
    }

    public function run() {
        $clientEvents = [
            'alert' => $this->alert,
            'finish' => $this->finish,
            'update' => $this->update,
        ];
        $this->view->registerJs('
            (function($) {
                var alerted = false;
                var options = '.\yii\helpers\Json::encode(['alertBefore' => $this->alertBefore]).'
                var callback = '.\yii\helpers\Json::encode($clientEvents).';
                $("['.self::DATA_DATE.']").each(function() {
                    var date = $(this).attr("'.self::DATA_DATE.'");
                    date = new Date(date);
                    $(this).countdown(date, function(event) {
                        $(this).html(event.strftime("'.$this->template.'"));
                    }).on("update.countdown", function(event) {
                        if (!alerted && options.alertBefore >= event.offset.totalSeconds) {
                            callback.alert(event);
                        }
                    }).on("finish.countdown", function(event) {
                        callback.finish(event);
                    });
                });
            })(jQuery);
        ');
        $options = [
            'id' => $this->id,
            'class' => 'countdown',
            self::DATA_DATE => $this->datetime,
        ];
        return Html::tag('div', '', $options);
    }
}