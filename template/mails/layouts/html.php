<?php
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this \yii\web\View view component instance */
/* @var $message \yii\mail\MessageInterface the message being composed */
/* @var $content string main view render result */

/*$bundle = \project\themes\event\assets\ThemeAsset::register($this);

$theme = $this->theme;
$theme = Yii::createObject('yii\base\Theme');
$theme->setBaseUrl(Url::to($bundle->baseUrl, true));
$theme->setBasePath($bundle->basePath);*/

$primaryColor = '#fe0c03';

?>
<?php $this->beginPage() ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0" />
    <meta http-equiv="Content-Type" content="text/html; charset=<?= Yii::$app->charset ?>" />
    <title><?= Html::encode($this->title) ?></title>
	<style>	
		a { color: <?= $primaryColor ?>; }
		@media only screen and (max-width: 300px){ 
			body {
				width:218px !important;
				margin:auto !important;
				font-family: "微软雅黑";
			}
			.table {width:195px !important;margin:auto !important;}
			.logo, .titleblock, .linkbelow, .box, .footer, .space_footer{width:auto !important;display: block !important;}		
			span.title{font-size:20px !important;line-height: 23px !important}
			span.subtitle{font-size: 14px !important;line-height: 18px !important;padding-top:10px !important;display:block !important;}		
			td.box p{font-size: 12px !important;font-weight: bold !important;}
			.table-recap table, .table-recap thead, .table-recap tbody, .table-recap th, .table-recap td, .table-recap tr { 
				display: block !important; 
			}
			.table-recap{width: 200px!important;}
			.table-recap tr td, .conf_body td{text-align:center !important;}	
			.address{display: block !important;margin-bottom: 10px !important;}
			.space_address{display: none !important;}	
		}
		@media only screen and (min-width: 301px) and (max-width: 500px) {
			body {width:308px!important;margin:auto!important;}
			.table {width:285px!important;margin:auto!important;}	
			.logo, .titleblock, .linkbelow, .box, .footer, .space_footer{width:auto!important;display: block!important;}	
			.table-recap table, .table-recap thead, .table-recap tbody, .table-recap th, .table-recap td, .table-recap tr { 
			display: block !important; 
			}
			.table-recap{width: 293px !important;}
			.table-recap tr td, .conf_body td{text-align:center !important;}
		}
		@media only screen and (min-width: 501px) and (max-width: 768px) {
			body {width:478px!important;margin:auto!important;}
			.table {width:450px!important;margin:auto!important;}	
			.logo, .titleblock, .linkbelow, .box, .footer, .space_footer{width:auto!important;display: block!important;}			
		}
		@media only screen and (max-device-width: 480px) { 
			body {width:308px!important;margin:auto!important;}
			.table {width:285px;margin:auto!important;}	
			.logo, .titleblock, .linkbelow, .box, .footer, .space_footer{width:auto!important;display: block!important;}

			.table-recap{width: 285px!important;}
			.table-recap tr td, .conf_body td{text-align:center!important;}	
			.address{display: block !important;margin-bottom: 10px !important;}
			.space_address{display: none !important;}	
		}
	</style>
    <?php $this->head() ?>
</head>
<body style="-webkit-text-size-adjust:none;background-color:#ddd; width:800px;font-family:Open-sans, sans-serif;color:#555454;font-size:13px;line-height:18px;margin:auto;">
    <?php $this->beginBody() ?>
		<table class="table table-mail" style="width:100%; margin-top:10px;">
			<tr>
				<td align="center" class="logo" style="padding:15px 3px; display: table-cell!important" colspan="5">
					<a title="<?= Yii::$app->name ?>" href="<?= Url::base(true)  ?>" style="color:#337ff1">
						<img src="<?= $this->theme->getUrl('images/logo.png') ?>" alt="<?= Yii::$app->name ?>" />
					</a>
				</td>
			</tr>
			<tr>
				<td class="space" style="width:20px;padding:7px 0">&nbsp;</td>
				<td align="center" style="padding:7px 0">
					<table class="table" bgcolor="#ffffff" style="width:100%;  padding: 10px;">

						<tr>
							<td align="left" class="titleblock" style="padding:7px 10px">
								<?= $content ?>
							</td>
						</tr>

						<tr>
							<td class="space_footer" style="padding:0!important">&nbsp;</td>
						</tr>
					</table>
				</td>
				<td class="space" style="width:20px;padding:7px 0">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="5" class="footer" align="center" style="padding:7px 3px; display: table-cell!important; ">
					<span>
						&copy; <?= date('Y') ?> <?= Yii::$app->name ?>
						<?php /*
						<?= Yii::t('app', Yii::$app->config->getAppConfig('copyright'), array('{name}' => Yii::$app->name));?>
						*/ ?>
					</span>
				</td>
			</tr>
		</table>
    <?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>