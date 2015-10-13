<?php

require_once __DIR__.'/../../vendor/autoload.php';

class PrestashopBackOfficeTest extends Sauce\Sausage\WebDriverTestCase
{
    protected $base_url;

    public static $browsers = array(
        array(
            'browserName' => 'chrome',
            'desiredCapabilities' => array(
                'platform' => 'Linux',
            ),
        ),
    );

    public function __construct()
    {
        parent::__construct();

        $this->base_url = sprintf('%s://%s%s',
            getenv('TEST_PROTO'),
            getenv('TEST_HOST'),
            rtrim(getenv('TEST_BASE_DIR'), '/')
        );
    }

    public function setUpPage()
    {
        $this->timeouts()->implicitWait(10000);
        $this->timeouts()->asyncScript(10000);

        $this->url($this->base_url);
    }

    public function testBackOfficeTitle()
    {
        $this->url('/_admin/');

        $this->assertContains('Administration panel', $this->title());
    }

    public function testLoginFormExists()
    {
        $this->url('/_admin/');

        $email = $this->byName('email');
        $password = $this->byName('password');
        $submit = $this->byName('submitLogin');

        $this->assertEquals('', $email->value());
        $this->assertEquals('', $password->value());
        $this->assertEquals('submit', $submit->type());
    }

    public function testSubmitToSelf()
    {
        $this->url('/_admin/');

        // create a form object for reuse
        $form = $this->byId('login_form');

        // get the form action
        $action = $form->attribute('action');

        // check the action value
        $this->assertEquals('#', $action);

        // fill in the form field values
        $this->byName('email')->value('test@example.com');
        $this->byName('password')->value('0123456789');

        // submit the form
        $form->submit();

        $this->timeouts()->waitForPageToLoad();

        // check if form was posted
        $success = $this->byCssSelector('body.ps_back-office')->text();

        // check the value
        $this->assertContains('Dashboard', $success);
    }
}