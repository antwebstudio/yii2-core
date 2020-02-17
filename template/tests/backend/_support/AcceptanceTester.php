<?php
namespace tests\backend;

/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
*/
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;
	
   /**
    * Define custom actions here
    */
	public function login($username, $password)
	{
		if (!isset($password) || $password == '') throw new \Exception('Password for developer is not set in .env file. ');
		
		// if snapshot exists - skipping login
		if ($this->loadSessionSnapshot('login')) return;

		// logging in
		$this->amOnBackend('site/login');
		$this->seeInCurrentUrl('/site/login');
		
		$this->submitForm('form', [
			'LoginForm[username]' => $username,
			'LoginForm[password]' => $password,
		]);
		
		$this->wait(5);
		
		$this->dontSeeInCurrentUrl('/site/login');
		
		// saving snapshot
		$this->saveSessionSnapshot('login');
	}
	
	public function amOnFrontend($path) {
		$baseUrl = $this->getBaseUrl();

		$url = env('FRONTEND_URL');
		$url = strpos($baseUrl, 'localhost') === false ? '@baseUrl' : '@baseUrl';
		$url = str_replace('@baseUrl', $this->getBaseUrl(), $url);
		
		$this->amOnUrl($url.'/'.$path);
		
		$this->resetUrl();
	}
	
	public function amOnBackend($path) {
		$baseUrl = $this->getBaseUrl();

		$url = env('BACKEND_URL');
		$url = strpos($baseUrl, 'localhost') === false ? '@baseUrl/admin' : '@baseUrl/admin';
		$url = str_replace('@baseUrl', $this->getBaseUrl(), $url);
		
		$this->amOnUrl($url.'/'.$path);
		
		$this->resetUrl();
	}
}
