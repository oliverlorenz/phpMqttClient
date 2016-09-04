<?php
use oliverlorenz\reactphpmqtt\packet\ConnectionOptions;
use oliverlorenz\reactphpmqtt\packet\QoS\Levels;

/**
 * Class ConnectionOptionsTest
 *
 * @group connection-options
 *
 * @author Alin Eugen Deac <ade@vestergaardcompany.com>
 */
class ConnectionOptionsTest extends PHPUnit_Framework_TestCase
{

    /***********************************************************
     * Helpers
     **********************************************************/

    /**
     * Returns a new instance of the connection options
     *
     * @param array $options [optional]
     *
     * @return ConnectionOptions
     */
    protected function makeConnectionOptions(array $options = [])
    {
        return new ConnectionOptions($options);
    }

    /***********************************************************
     * Actual tests
     **********************************************************/

    public function testCanObtainInstance()
    {
        // Make sure that constructor does not fail
        $options = $this->makeConnectionOptions();
        $this->assertNotNull($options);
    }

    public function testCanPopulateViaArray()
    {
        $data = [
            'username'      =>  'john',
            'password'      =>  '123456',
            'clientId'      =>  uniqid(),
            'cleanSession'  =>  false,
            'willTopic'     =>  'lost/',
            'willMessage'   =>  'John has left...',
            'willQos'       =>  Levels::EXACTLY_ONCE_DELIVERY,
            'willRetain'    =>  true
        ];

        $options = $this->makeConnectionOptions($data);

        $this->assertSame($data['username'], $options->username, 'Incorrect username set');
        $this->assertSame($data['password'], $options->password, 'Incorrect password set');
        $this->assertSame($data['clientId'], $options->clientId, 'Incorrect client id set');
        $this->assertSame($data['cleanSession'], $options->cleanSession, 'Incorrect clean session state set');
        $this->assertSame($data['willTopic'], $options->willTopic, 'Incorrect will topic set');
        $this->assertSame($data['willMessage'], $options->willMessage, 'Incorrect will message set');
        $this->assertSame($data['willQos'], $options->willQos, 'Incorrect will quality of service set');
        $this->assertSame($data['willRetain'], $options->willRetain, 'Incorrect will retain state set');
    }
}