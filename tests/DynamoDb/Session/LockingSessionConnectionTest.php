<?php
namespace Aws\Test\DynamoDb\Session;

use Aws\DynamoDb\Session\LockingSessionConnection;
use Aws\Common\Result;
use Aws\Test\UsesServiceTrait;

/**
 * @covers Aws\DynamoDb\Session\LockingSessionConnection
 */
class LockingSessionConnectionTest extends \PHPUnit_Framework_TestCase
{
    use UsesServiceTrait;

    public function testReadRetrievesItemData()
    {
        $client = $this->getTestSdk()->getDynamoDb();
        $this->addMockResults($client, [
            $this->createMockAwsException(
                'ConditionalCheckFailedException',
                'Aws\DynamoDb\Exception\DynamoDbException'
            ),
            new Result(['Attributes' => [
                'sessionid' => ['S' => 'session1'],
                'otherkey'  => ['S' => 'foo'],
            ]]),
        ]);

        $connection = new LockingSessionConnection($client);
        $data = $connection->read('session1');

        $this->assertEquals(
            ['sessionid' => 'session1', 'otherkey' => 'foo'],
            $data
        );
    }
}
