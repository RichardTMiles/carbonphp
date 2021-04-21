<?php /** @noinspection PhpUndefinedClassInspection */
/**
 * Created by IntelliJ IDEA.
 * User: rmiles
 * Date: 6/26/2018
 * Time: 3:21 PM
 */

declare(strict_types=1);

namespace Tests;

use CarbonPHP\Database;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Rest;
use CarbonPHP\Tables\Carbon_Location_References;
use CarbonPHP\Tables\Carbon_Locations;
use CarbonPHP\Tables\Carbon_User_Tasks;
use CarbonPHP\Tables\Carbon_Users as Users;
use CarbonPHP\Tables\Carbons;
use CarbonPHP\Tables\History_Logs;
use CarbonPHP\Tables\Sessions;


/**
 * @runTestsInSeparateProcesses
 */
final class RestTest extends Config
{

    public static array $restChallenge = [];

    public static function createUser(): string
    {
        self::assertInternalType('string', $uid = Users::Post([
            Users::USER_TYPE => 'Athlete',
            Users::USER_IP => '127.0.0.1',
            Users::USER_SPORT => 'GOLF',
            Users::USER_EMAIL_CONFIRMED => 1,
            Users::USER_USERNAME => Config::ADMIN_USERNAME,
            Users::USER_PASSWORD => Config::ADMIN_PASSWORD,
            Users::USER_EMAIL => 'richard@miles.systems',
            Users::USER_FIRST_NAME => 'Richard',
            Users::USER_LAST_NAME => 'Miles',
            Users::USER_GENDER => 'Male'
        ]), 'No string ID was returned');

        return $uid;
    }

    /**
     * @param string $key
     * @throws PublicAlert
     */
    private function KeyExistsAndRemove(string $key): void
    {
        $store = [];

        self::assertTrue(Carbons::Get($store, $key, []),
            'Failed to find key (' . $key . ') in table carbons. Check that post is committed.');

        if (!empty($store)) {
            self::assertTrue(
                Carbons::Delete($store, $key, []),
                'Rest api failed to remove the test key ' . $key
            );
        }
    }


    /**
     * @throws PublicAlert
     */
    public function testRestApiCanPostAndDelete(): void
    {
        // Should return a unique hex id
        self::assertInternalType('string', $key = Carbons::Post([Carbons::ENTITY_TAG => self::class]));
        $ref = [];
        self::assertTrue($key = Carbons::Delete($ref, $key));
    }


    /**
     * @throws PublicAlert
     */
    public function testRestApiCanGet(): void
    {
        $return = [];
        self::assertTrue(Carbons::Get($return, $key = Carbons::Post([
            Carbons::ENTITY_TAG => self::class
        ])));

        self::assertInternalType('array', $return);

        self::assertNotEmpty($return);

        self::assertArrayHasKey(Carbons::COLUMNS[Carbons::ENTITY_FK], $return);
        self::assertArrayHasKey(Carbons::COLUMNS[Carbons::ENTITY_TAG], $return);
        self::assertArrayHasKey(Carbons::COLUMNS[Carbons::ENTITY_PK], $return);

        $return = [];

        self::assertTrue(Carbons::Get($return, null, [
            Rest::WHERE => [
                Carbons::ENTITY_TAG => self::class
            ],
            Rest::PAGINATION => [
                Rest::LIMIT => 1
            ]
        ]));

        self::assertNotEmpty($return);

        Carbons::Delete($return, $key);

        self::assertEmpty($return);
    }


    /**
     * @depends testRestApiCanGet
     * @throws PublicAlert
     */
    public function testRestApiCanAggregate(): void
    {
        $return = [];

        self::assertTrue(Carbons::Get($return, $key = Carbons::Post([
            Carbons::ENTITY_TAG => self::class
        ])));

        $temp = [];

        self::assertTrue(Carbons::Get($temp, null, [
            Rest::WHERE => [
                Carbons::ENTITY_TAG => self::class
            ],
            Rest::PAGINATION => [
                Rest::LIMIT => 1
            ]
        ]));

        self::assertArrayHasKey(Carbons::COLUMNS[Carbons::ENTITY_PK], $temp);

        $temp = [];

        self::assertTrue(Carbons::Get($temp, null, [
            Carbons::SELECT => [
                [Rest::COUNT, Carbons::ENTITY_PK, Carbons::COLUMNS[Carbons::ENTITY_PK]]
            ],
            Carbons::PAGINATION => [
                Carbons::LIMIT => 1
            ]
        ]));

        self::assertArrayHasKey(Carbons::COLUMNS[Carbons::ENTITY_PK], $temp, 'failed on PAGINATION:LIMIT');

        self::assertTrue(Carbons::Get($temp, null, [
            Carbons::SELECT => [
                [Rest::COUNT, Carbons::ENTITY_PK, Carbons::COLUMNS[Carbons::ENTITY_PK]]
            ],
            Carbons::PAGINATION => [
                Carbons::LIMIT => 2   // check the limit
            ]
        ]));

        self::assertArrayHasKey(0, $temp);
        self::assertArrayHasKey(Carbons::COLUMNS[Carbons::ENTITY_PK], $temp[0], 'failed on PAGINATION:LIMIT');

    }

    /**
     * @throws PublicAlert
     */
    public function testRestApiCanPut(): void
    {
        $store = [];

        self::assertNotEmpty($primary = Carbons::Post([]));

        self::assertTrue(Carbons::Get($store, $primary, []));

        self::assertArrayHasKey(Carbons::COLUMNS[Carbons::ENTITY_FK], $store);

        self::assertTrue(
            Carbons::Put($store, $store[Carbons::COLUMNS[Carbons::ENTITY_PK]], [
                Carbons::ENTITY_TAG => $primary
            ]), 'Failed Updating Records.');

        self::assertTrue(
            Carbons::Put($store, $store[Carbons::COLUMNS[Carbons::ENTITY_TAG]], [
                Carbons::ENTITY_TAG => $goodStuff = 'GOOD STUFF'
            ]), 'Failed Updating Records With Identical Data. See https://stackoverflow.com/questions/10522520/pdo-were-rows-affected-during-execute-statement ');

        self::assertEquals($goodStuff, $store[Carbons::COLUMNS[Carbons::ENTITY_TAG]]);

        $store = [];

        self::assertTrue(Carbons::Get($store, $primary, []));

        self::assertArrayHasKey(Carbons::COLUMNS[Carbons::ENTITY_PK], $store,
            'Failed to see updated record in database.');

    }


    /**
     * @throws PublicAlert
     * @depends testRestApiCanPostAndDelete
     */
    public function testRestApiCanJoin(): void
    {
        $user = [];

        if (Users::Get($user, null, [
                Users::SELECT => [
                    Users::USER_ID
                ],
                Users::WHERE => [
                    Users::USER_USERNAME => Config::ADMIN_USERNAME
                ],
                Users::PAGINATION => [
                    Users::LIMIT => 1
                ]
            ]) && !empty($user)) {
            self::assertTrue(Users::Delete($user, $user[Users::COLUMNS[Users::USER_ID]], []),
                'Failed to delete user for join test.');
        }

        Rest::$commit = false;

        $uid = self::createUser();

        self::assertInternalType('string', $lid = Carbon_Locations::Post([
            Carbon_Locations::CITY => 'Grapevine',
            Carbon_Locations::STATE => 'Texas',
            Carbon_Locations::ZIP => 76051
        ]), 'Failed to create location entity.');

        Rest::$commit = true; // the next post request will post

        self::assertTrue(Carbon_Location_References::Post([
            Carbon_Location_References::ENTITY_REFERENCE => $uid,
            Carbon_Location_References::LOCATION_REFERENCE => $lid
        ]), 'Failed to create location references.');

        $user = [];

        $db = Database::database();

        self::assertFalse($db->inTransaction(), 'Failed closing transaction');

        self::assertTrue(Users::Get($user, $uid));

        self::assertArrayHasKey(Users::COLUMNS[Users::USER_ABOUT_ME] , $user);

        self::assertTrue(Users::Get($user, $uid, [
            Users::SELECT => [
                Users::USER_USERNAME,
                Carbon_Locations::STATE
            ],
            Users::JOIN => [
                Users::INNER => [
                    Carbon_Location_References::TABLE_NAME => [
                        Users::USER_ID => Carbon_Location_References::ENTITY_REFERENCE
                    ],
                    Carbon_Locations::TABLE_NAME => [
                        Carbon_Locations::ENTITY_ID => Carbon_Location_References::LOCATION_REFERENCE
                    ]
                ]
            ],
            Users::PAGINATION => [
                Users::LIMIT => 1,
                Users::ORDER => [Users::USER_USERNAME => Users::ASC]
            ]
        ]), 'Failed to run inner join.');

        self::assertArrayHasKey(Users::COLUMNS[Users::USER_USERNAME], $user);

        self::assertEquals(Config::ADMIN_USERNAME, $user[Users::COLUMNS[Users::USER_USERNAME]]);

        self::assertEquals('Texas', $user[Carbon_Locations::COLUMNS[Carbon_Locations::STATE]]);
    }


    /**
     * This test undoubtedly does a half ass job at verifying order of operations,
     * expected return values for custom functions, custom method and validation preservation.
     * This will end up breaking and causing me to add another 40 lines.
     * @throws PublicAlert
     * @depends testRestApiCanJoin
     */
    public function testRestApiCanUseUserDefinedCallbacks(): void
    {
        $user = [];

        self::assertTrue(Users::Get($user, null, [
                Rest::WHERE => [
                    Users::USER_USERNAME => Config::ADMIN_USERNAME,
                    Users::USER_PASSWORD => Config::ADMIN_PASSWORD
                ],
                Rest::PAGINATION => [
                    Rest::LIMIT => 1
                ]
            ]
        ), 'The user could not be retrieved.');

        $uid = $user[Users::COLUMNS[Users::USER_ID]];

        self::assertNotEmpty($uid, 'The user id was empty.');

        self::assertEmpty(self::$restChallenge, 'Rest Challenges Should Start as Empty.');

        $id = Carbon_User_Tasks::Post([
            Carbon_User_Tasks::USER_ID => $uid,
            Carbon_User_Tasks::TASK_NAME => 'Hello World',
            Carbon_User_Tasks::TASK_DESCRIPTION => 'Test',
            Carbon_User_Tasks::PERCENT_COMPLETE => 70
        ]);

        self::assertCount(7, self::$restChallenge, 'Not all rest challenges have run');

        self::assertArrayHasKey(0, self::$restChallenge);
        self::assertArrayHasKey(1, self::$restChallenge);
        self::assertArrayHasKey(2, self::$restChallenge);
        self::assertArrayHasKey(3, self::$restChallenge);
        self::assertArrayHasKey(4, self::$restChallenge);
        self::assertArrayHasKey(5, self::$restChallenge);
        self::assertArrayHasKey(Carbon_User_Tasks::USER_ID, self::$restChallenge[0][0]);
        self::assertArrayHasKey(Carbon_User_Tasks::TASK_NAME, self::$restChallenge[0][0]);
        self::assertArrayHasKey(Carbon_User_Tasks::TASK_DESCRIPTION, self::$restChallenge[0][0]);
        self::assertArrayHasKey(Carbon_User_Tasks::PERCENT_COMPLETE, self::$restChallenge[0][0]);
        self::assertArrayHasKey(1, self::$restChallenge[1]);
        self::assertEquals(Rest::POST, self::$restChallenge[1][1]); // start at 0 ;)
        self::assertEquals(Rest::PREPROCESS, self::$restChallenge[1][2]); // start at 0 ;)
        self::assertEquals(Carbon_User_Tasks::PERCENT_COMPLETE, self::$restChallenge[3][1]);
    }


    /**
     * @depends testRestApiCanJoin
     * @throws PublicAlert
     */
    public function testRestApiCanSubQuery(): void
    {

        $user = [];
        self::assertTrue(Carbons::Get($user, null, [
            Carbons::SELECT => [
                Carbons::ENTITY_PK
            ],
            Carbons::WHERE => [
                Carbons::ENTITY_PK =>
                    Users::subSelect(null, [
                        Users::SELECT => [
                            Users::USER_ID
                        ],
                        Users::WHERE => [
                            Users::USER_USERNAME => Config::ADMIN_USERNAME
                        ]
                    ])
            ],
            Carbons::PAGINATION => [
                Carbons::LIMIT =>
                    1
            ]
        ]));

        self::assertNotEmpty($user, 'Could not get user admin via sub query.');

        self::assertArrayHasKey(Carbons::COLUMNS[Carbons::ENTITY_PK], $user);

    }


    /**
     * @depends testRestApiCanPostAndDelete
     */
    public function testExternalRequestValidationRoutines(): void
    {

        $_POST = [
            Users::SELECT => [
                Users::USER_USERNAME,
                Carbon_Locations::STATE,
            ],
            Users::JOIN => [
                Users::INNER => [
                    Carbon_Location_References::TABLE_NAME => [
                        Users::USER_ID,
                        Carbon_Location_References::ENTITY_REFERENCE
                    ],
                    Carbon_Locations::TABLE_NAME => [
                        Carbon_Locations::ENTITY_ID,
                        Carbon_Location_References::LOCATION_REFERENCE
                    ]
                ]
            ],
            Users::PAGINATION => [
                Users::LIMIT => 10,
                Users::ORDER => [Users::USER_USERNAME, Users::ASC] // todo - I think Users::USER_USERNAME . Users::ASC worked, or didnt throw an error..
            ]
        ];


        $_SERVER['REQUEST_METHOD'] = 'GET';

        ob_start(null, 0, PHP_OUTPUT_HANDLER_CLEANABLE | PHP_OUTPUT_HANDLER_FLUSHABLE | PHP_OUTPUT_HANDLER_REMOVABLE);

        Rest::ExternalRestfulRequestsAPI(Users::TABLE_NAME, null, Users::CLASS_NAMESPACE);

        $out = trim(ob_get_clean());

        self::assertNotEmpty($GLOBALS['json']['rest']);

        self::assertStringEndsWith('}', $out, 'Did not detect json output. OUTPUT :: ' . $out);

    }


    /**
     * TODO - this could be better showcasing more things
     * @throws PublicAlert
     * @depends testRestApiCanPostAndDelete
     */
    public function testCascadeDelete(): void
    {
        $user = [Users::USER_USERNAME => Config::ADMIN_USERNAME];

        self::assertTrue(Users::Delete($user, null, [
            Users::USER_USERNAME => Config::ADMIN_USERNAME
        ]));

        self::assertEmpty($user, 'Could not delete user admin in cascade delete function.');

        self::assertInternalType('array', $user, 'Delete functions did not clear provided array to 
        empty array.');


        self::assertTrue(Users::Get($user, null, [
            Users::WHERE => [
                Users::USER_USERNAME => Config::ADMIN_USERNAME
            ]
        ]));

        self::assertTrue(Users::Delete($user, '8544e3d581ba11e8942cd89ef3fc55fb', []),
            'Test can delete by primary key.php');

        self::assertEmpty($user, 'Cascade delete failed.');
    }

    public function testRestApiCanUseNonCarbonPrimaryKeys(): void
    {
        $return = [];

        self::assertNotFalse(Sessions::Post([
            Sessions::USER_ID => $USER_ID = Carbons::Post([]),
            Sessions::USER_IP => '127.0.0.1',
            Sessions::SESSION_ID => $SESSION_ID = Carbons::Post([]),
            Sessions::SESSION_EXPIRES => date('Y-m-d H:i:s'), // @link https://stackoverflow.com/questions/2215354/php-date-format-when-inserting-into-datetime-in-mysql/17295570
            Sessions::SESSION_DATA => '',
            Sessions::USER_ONLINE_STATUS => 1
        ]));

        self::assertTrue(Sessions::Put($return, $SESSION_ID, [
            Sessions::USER_IP => '127.0.0.2',
            Sessions::USER_ONLINE_STATUS => 0
        ]));

        // todo - check array merge
        self::assertTrue(Sessions::Get($return, $SESSION_ID, []));
        self::assertTrue(Sessions::Delete($return, $SESSION_ID));
        self::assertTrue(Carbons::Delete($return, $SESSION_ID));
        self::assertTrue(Carbons::Delete($return, $USER_ID));
    }


    /**
     * It can be noted that history logs are not Carbon tables as the reff will be deleted before it
     * is added to this table.
     * @throws PublicAlert
     */
    public function testRestApiCanUseTablesWithNoPrimaryKey(): void
    {
        $ignore = [];
        $condition = 'ME';

        // Should return a unique hex id
        self::assertTrue(History_Logs::Post([
            History_Logs::RESOURCE_TYPE => $condition,
            History_Logs::RESOURCE_UUID => $RESOURCE_UUID = Carbons::Post([]),
            History_Logs::UUID => $uuid = Carbons::Post([]),
            History_Logs::DATA => '{}'
        ]));

        // Should return a unique hex id
        self::assertTrue(History_Logs::Put($ignore, [
            Rest::UPDATE => [
                History_Logs::DATA => '',
            ],
            Rest::WHERE => [
                History_Logs::RESOURCE_UUID => $RESOURCE_UUID,
            ]
        ]));

        $return = [];

        self::assertTrue(History_Logs::Get($return, [
            Rest::WHERE => [
                History_Logs::RESOURCE_TYPE => $condition
            ],
            Rest::PAGINATION => [
                Rest::LIMIT => 1,
                Rest::ORDER => [History_Logs::UUID => Rest::ASC]
            ]
        ]));

        self::assertCount(5, $return);
    }

    public function testRestApiCanUseJson(): void
    {
        $ignore = [];
        $condition = 'ME';

        self::assertTrue(History_Logs::Post([
            History_Logs::RESOURCE_TYPE => $condition,
            History_Logs::RESOURCE_UUID => $RESOURCE_UUID = Carbons::Post([]),
            History_Logs::UUID => $UUID = Carbons::Post([]),
            History_Logs::DATA => [
                'Test' => 'Value'
            ]
        ]));


        // Should return a unique hex id
        self::assertTrue(History_Logs::Put($ignore, [
            Rest::UPDATE => [
                History_Logs::RESOURCE_UUID => '8544e3d581ba11e8942cd89ef3fc55fb',
                History_Logs::UUID => '8544e3d581ba11e8942cd89ef3fc55fb',
                History_Logs::DATA => [
                    'Test' => 'Value'
                ]
            ],
            Rest::WHERE => [
                History_Logs::RESOURCE_TYPE => $condition,
            ]
        ]));

        $return = [];

        self::assertTrue(History_Logs::Get($return, [
            Rest::WHERE => [
                History_Logs::RESOURCE_TYPE => $condition
            ],
            Rest::PAGINATION => [
                Rest::LIMIT => 1,
                Rest::ORDER => [History_Logs::UUID => Rest::ASC]
            ]
        ]));

        self::assertCount(5, $return);


    }

}