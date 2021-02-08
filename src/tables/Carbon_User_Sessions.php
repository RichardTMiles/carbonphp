<?php 

namespace CarbonPHP\Tables;

// Restful defaults
use CarbonPHP\Database;
use CarbonPHP\Error\PublicAlert;
use CarbonPHP\Interfaces\iRest;
use CarbonPHP\Rest;
use PDO;
use function array_key_exists;
use function count;
use function func_get_args;
use function is_array;

// Custom User Imports


class Carbon_User_Sessions extends Rest implements iRest
{
    
    public const CLASS_NAME = 'Carbon_User_Sessions';
    public const CLASS_NAMESPACE = 'CarbonPHP\Tables\\';
    public const TABLE_NAME = 'carbon_user_sessions';
    public const TABLE_PREFIX = '';
    
    public const USER_ID = 'carbon_user_sessions.user_id'; 
    public const USER_IP = 'carbon_user_sessions.user_ip'; 
    public const SESSION_ID = 'carbon_user_sessions.session_id'; 
    public const SESSION_EXPIRES = 'carbon_user_sessions.session_expires'; 
    public const SESSION_DATA = 'carbon_user_sessions.session_data'; 
    public const USER_ONLINE_STATUS = 'carbon_user_sessions.user_online_status'; 

    public const PRIMARY = [
        'carbon_user_sessions.session_id',
    ];

    public const COLUMNS = [
        'carbon_user_sessions.user_id' => 'user_id','carbon_user_sessions.user_ip' => 'user_ip','carbon_user_sessions.session_id' => 'session_id','carbon_user_sessions.session_expires' => 'session_expires','carbon_user_sessions.session_data' => 'session_data','carbon_user_sessions.user_online_status' => 'user_online_status',
    ];

    public const PDO_VALIDATION = [
        'carbon_user_sessions.user_id' => ['binary', '2', '16'],'carbon_user_sessions.user_ip' => ['binary', '2', '16'],'carbon_user_sessions.session_id' => ['varchar', '2', '255'],'carbon_user_sessions.session_expires' => ['datetime', '2', ''],'carbon_user_sessions.session_data' => ['text,', '2', ''],'carbon_user_sessions.user_online_status' => ['tinyint', '0', '1'],
    ];
     
    /**
     * PHP validations works as follows:
     *  The first index '0' of PHP_VALIDATIONS will run after REGEX_VALIDATION's but
     *  before every other validation method described here below.
     *  The other index positions are respective to the request method calling the ORM
     *  or column which maybe present in the request.
     *  Column names using the 1 to 1 constants in the class maybe used for global
     *  specific methods when under PHP_VALIDATION, or method specific operations when under
     *  its respective request method, which only run when the column is requested or acted on.
     *  Global functions and method specific functions will receive the full request which
     *  maybe acted on by reference. All column specific validation methods will only receive
     *  the associated value given in the request which may also be received by reference.
     *  All methods MUST be declared as static.
     */
 
    public const PHP_VALIDATION = [ 
        [self::DISALLOW_PUBLIC_ACCESS],
        self::GET => [ self::DISALLOW_PUBLIC_ACCESS ],    
        self::POST => [ self::DISALLOW_PUBLIC_ACCESS ],    
        self::PUT => [ self::DISALLOW_PUBLIC_ACCESS ],    
        self::DELETE => [ self::DISALLOW_PUBLIC_ACCESS ],    
    ]; 
 
    public const REGEX_VALIDATION = []; 
   

    
    public static function createTableSQL() : string {
    return /** @lang MySQL */ <<<MYSQL
    CREATE TABLE `carbon_user_sessions` (
  `user_id` binary(16) NOT NULL,
  `user_ip` binary(16) DEFAULT NULL,
  `session_id` varchar(255) NOT NULL,
  `session_expires` datetime NOT NULL,
  `session_data` text,
  `user_online_status` tinyint(1) DEFAULT '1',
  PRIMARY KEY (`session_id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;
MYSQL;
    }
    
    
    /**
    *
    *   $argv = [
    *       Rest::SELECT => [
    *              ]'*column name array*', 'etc..'
    *        ],
    *
    *       Rest::WHERE => [
    *              'Column Name' => 'Value To Constrain',
    *              'Defaults to AND' => 'Nesting array switches to OR',
    *              [
    *                  'Column Name' => 'Value To Constrain',
    *                  'This array is OR'ed together' => 'Another sud array would `AND`'
    *                  [ etc... ]
    *              ]
    *        ],
    *        Rest::JOIN => [
    *            Rest::INNER => [
    *                Carbon_Users::CLASS_NAME => [
    *                    'Column Name' => 'Value To Constrain',
    *                    'Defaults to AND' => 'Nesting array switches to OR',
    *                    [
    *                       'Column Name' => 'Value To Constrain',
    *                       'This array is OR'ed together' => 'value'
    *                       [ 'Another sud array would `AND`ed... ]
    *                    ],
    *                    [ 'Column Name', Rest::LESS_THAN, 'Another Column Name']
    *                ]
    *            ],
    *            Rest::LEFT_OUTER => [
    *                Example_Table::CLASS_NAME => [
    *                    Location::USER_ID => Users::ID,
    *                    Location_References::ENTITY_KEY => $custom_var,
    *                   
    *                ],
    *                Example_Table_Two::CLASS_NAME => [
    *                    ect... 
    *                ]
    *            ]
    *        ],
    *        Rest::PAGINATION => [
    *              Rest::LIMIT => (int) 90, // The maximum number of rows to return,
    *                       setting the limit explicitly to 1 will return a key pair array of only the
    *                       singular result. SETTING THE LIMIT TO NULL WILL ALLOW INFINITE RESULTS (NO LIMIT).
    *                       The limit defaults to 100 by design.
    *
    *               Rest::ORDER => ['*column name*' => Rest::ASC ],  // i.e.  'username' => Rest::DESC
    *         ],
    *
    *   ];
    *
    *
    * @param array $return
    * @param string|null $primary
    * @param array $argv
    * @throws PublicAlert
    * @return bool
    */
    public static function Get(array &$return, string $primary = null, array $argv = []): bool
    {
        self::startRest(self::GET, $argv);

        $pdo = self::database();

        $sql = self::buildSelectQuery($primary, $argv, '', $pdo);
        
        self::jsonSQLReporting(func_get_args(), $sql);
        
        $stmt = $pdo->prepare($sql);

        self::bind($stmt);

        if (!$stmt->execute()) {
            throw new PublicAlert('Failed to execute the query on Carbon_User_Sessions.', 'danger');
        }

        $return = $stmt->fetchAll(PDO::FETCH_ASSOC);

        /**
        *   The next part is so every response from the rest api
        *   formats to a set of rows. Even if only one row is returned.
        *   You must set the third parameter to true, otherwise '0' is
        *   apparently in the self::PDO_VALIDATION
        */

        
        if ($primary !== null || (isset($argv[self::PAGINATION][self::LIMIT]) && $argv[self::PAGINATION][self::LIMIT] === 1 && count($return) === 1)) {
            $return = isset($return[0]) && is_array($return[0]) ? $return[0] : $return;
            // promise this is needed and will still return the desired array except for a single record will not be an array
        
        }

        self::postprocessRestRequest($return);
        self::completeRest();
        return true;
    }

    /**
     * @param array $argv
     * @param string|null $dependantEntityId - a C6 Hex entity key 
     * @return bool|string
     * @throws PublicAlert
     */
    public static function Post(array $argv, string $dependantEntityId = null)
    {   
        self::startRest(self::POST, $argv);
    
        foreach ($argv as $columnName => $postValue) {
            if (!array_key_exists($columnName, self::PDO_VALIDATION)) {
                throw new PublicAlert("Restful table could not post column $columnName, because it does not appear to exist.", 'danger');
            }
        } 
        
        $sql = 'INSERT INTO carbon_user_sessions (user_id, user_ip, session_id, session_expires, session_data, user_online_status) VALUES ( UNHEX(:user_id), UNHEX(:user_ip), :session_id, :session_expires, :session_data, :user_online_status)';

        self::jsonSQLReporting(func_get_args(), $sql);

        $stmt = self::database()->prepare($sql);

        
        
        
        
        if (!array_key_exists('carbon_user_sessions.user_id', $argv)) {
            throw new PublicAlert('Required argument "carbon_user_sessions.user_id" is missing from the request.', 'danger');
        }
        $user_id = $argv['carbon_user_sessions.user_id'];
        $ref='carbon_user_sessions.user_id';
        if (!self::validateInternalColumn(self::POST, $ref, $user_id)) {
            throw new PublicAlert('Your custom restful api validations caused the request to fail on column \'carbon_user_sessions.user_id\'.');
        }        
        $stmt->bindParam(':user_id',$user_id, 2, 16);
        
        
        
        $user_ip = $argv['carbon_user_sessions.user_ip'] ?? null;
        $ref='carbon_user_sessions.user_ip';
        if (!self::validateInternalColumn(self::POST, $ref, $user_ip, $user_ip === null)) {
            throw new PublicAlert('Your custom restful api validations caused the request to fail on column \'carbon_user_sessions.user_ip\'.');
        }        
        $stmt->bindParam(':user_ip',$user_ip, 2, 16);
        
        
        
        
        if (!array_key_exists('carbon_user_sessions.session_id', $argv)) {
            throw new PublicAlert('Required argument "carbon_user_sessions.session_id" is missing from the request.', 'danger');
        }
        $session_id = $argv['carbon_user_sessions.session_id'];
        $ref='carbon_user_sessions.session_id';
        if (!self::validateInternalColumn(self::POST, $ref, $session_id)) {
            throw new PublicAlert('Your custom restful api validations caused the request to fail on column \'carbon_user_sessions.session_id\'.');
        }        
        $stmt->bindParam(':session_id',$session_id, 2, 255);
        
                
        
        if (!array_key_exists('carbon_user_sessions.session_expires', $argv)) {
            throw new PublicAlert('The column \'carbon_user_sessions.session_expires\' is set to not null and has no default value. It must exist in the request and was not found in the one sent.');
        } 
        $ref='carbon_user_sessions.session_expires';
        if (!self::validateInternalColumn(self::POST, $ref, $session_expires)) {
            throw new PublicAlert('Your custom restful api validations caused the request to fail on column \'carbon_user_sessions.session_expires\'.');
        }
        $stmt->bindValue(':session_expires', $argv['carbon_user_sessions.session_expires'], 2);

        
                
        
        if (!array_key_exists('carbon_user_sessions.session_data', $argv)) {
            throw new PublicAlert('The column \'carbon_user_sessions.session_data\' is set to not null and has no default value. It must exist in the request and was not found in the one sent.');
        } 
        $ref='carbon_user_sessions.session_data';
        if (!self::validateInternalColumn(self::POST, $ref, $session_data)) {
            throw new PublicAlert('Your custom restful api validations caused the request to fail on column \'carbon_user_sessions.session_data\'.');
        }
        $stmt->bindValue(':session_data', $argv['carbon_user_sessions.session_data'], 2);

        
        
        
        $user_online_status = $argv['carbon_user_sessions.user_online_status'] ?? '1';
        $ref='carbon_user_sessions.user_online_status';
        if (!self::validateInternalColumn(self::POST, $ref, $user_online_status, $user_online_status === '1')) {
            throw new PublicAlert('Your custom restful api validations caused the request to fail on column \'carbon_user_sessions.user_online_status\'.');
        }        
        $stmt->bindParam(':user_online_status',$user_online_status, 0, 1);
        


        
        if ($stmt->execute()) {
            self::prepostprocessRestRequest();
            
            self::postprocessRestRequest();
            
            self::completeRest();
            
            return true;  
        }
        
        self::completeRest();
         
        return false;
    
    }
    
    /**
    * @param array $return
    * @param string $primary
    * @param array $argv
    * @throws PublicAlert
    * @return bool
    */
    public static function Put(array &$return, string $primary, array $argv) : bool
    {
        self::startRest(self::PUT, $argv);
        
        if (empty($primary)) {
            throw new PublicAlert('Restful tables which have a primary key must be updated by its primary key.', 'danger');
        }
        
        if (array_key_exists(self::UPDATE, $argv)) {
            $argv = $argv[self::UPDATE];
        }
        
        foreach ($argv as $key => &$value) {
            if (!array_key_exists($key, self::PDO_VALIDATION)){
                throw new PublicAlert('Restful table could not update column $key, because it does not appear to exist.', 'danger');
            }
            if (!self::validateInternalColumn(self::PUT, $key, $value)) {
                throw new PublicAlert('Your custom restful api validations caused the request to fail on column \'carbon_user_sessions.\'.');
            }
        }
        unset($value);

        $sql = /** @lang MySQLFragment */ 'UPDATE carbon_user_sessions SET '; // intellij cant handle this otherwise

        $set = '';

        if (array_key_exists('carbon_user_sessions.user_id', $argv)) {
            $set .= 'user_id=UNHEX(:user_id),';
        }
        if (array_key_exists('carbon_user_sessions.user_ip', $argv)) {
            $set .= 'user_ip=UNHEX(:user_ip),';
        }
        if (array_key_exists('carbon_user_sessions.session_id', $argv)) {
            $set .= 'session_id=:session_id,';
        }
        if (array_key_exists('carbon_user_sessions.session_expires', $argv)) {
            $set .= 'session_expires=:session_expires,';
        }
        if (array_key_exists('carbon_user_sessions.session_data', $argv)) {
            $set .= 'session_data=:session_data,';
        }
        if (array_key_exists('carbon_user_sessions.user_online_status', $argv)) {
            $set .= 'user_online_status=:user_online_status,';
        }
        
        $sql .= substr($set, 0, -1);

        $pdo = self::database();

        $sql .= ' WHERE  session_id='.self::addInjection($primary, $pdo).'';
        

        self::jsonSQLReporting(func_get_args(), $sql);

        $stmt = $pdo->prepare($sql);

        if (array_key_exists('carbon_user_sessions.user_id', $argv)) {
            $user_id = $argv['carbon_user_sessions.user_id'];
            $ref = 'carbon_user_sessions.user_id';
            if (!self::validateInternalColumn(self::PUT, $ref, $user_id)) {
                throw new PublicAlert('Your custom restful api validations caused the request to fail on column \'carbon_user_tasks.end_date\'.');
            }
            $stmt->bindParam(':user_id',$user_id, 2, 16);
        }
        if (array_key_exists('carbon_user_sessions.user_ip', $argv)) {
            $user_ip = $argv['carbon_user_sessions.user_ip'];
            $ref = 'carbon_user_sessions.user_ip';
            if (!self::validateInternalColumn(self::PUT, $ref, $user_ip)) {
                throw new PublicAlert('Your custom restful api validations caused the request to fail on column \'carbon_user_tasks.end_date\'.');
            }
            $stmt->bindParam(':user_ip',$user_ip, 2, 16);
        }
        if (array_key_exists('carbon_user_sessions.session_id', $argv)) {
            $session_id = $argv['carbon_user_sessions.session_id'];
            $ref = 'carbon_user_sessions.session_id';
            if (!self::validateInternalColumn(self::PUT, $ref, $session_id)) {
                throw new PublicAlert('Your custom restful api validations caused the request to fail on column \'carbon_user_tasks.end_date\'.');
            }
            $stmt->bindParam(':session_id',$session_id, 2, 255);
        }
        if (array_key_exists('carbon_user_sessions.session_expires', $argv)) {
            $stmt->bindValue(':session_expires',$argv['carbon_user_sessions.session_expires'], 2);
        }
        if (array_key_exists('carbon_user_sessions.session_data', $argv)) {
            $stmt->bindValue(':session_data',$argv['carbon_user_sessions.session_data'], 2);
        }
        if (array_key_exists('carbon_user_sessions.user_online_status', $argv)) {
            $user_online_status = $argv['carbon_user_sessions.user_online_status'];
            $ref = 'carbon_user_sessions.user_online_status';
            if (!self::validateInternalColumn(self::PUT, $ref, $user_online_status)) {
                throw new PublicAlert('Your custom restful api validations caused the request to fail on column \'carbon_user_tasks.end_date\'.');
            }
            $stmt->bindParam(':user_online_status',$user_online_status, 0, 1);
        }

        self::bind($stmt);

        if (!$stmt->execute()) {
            throw new PublicAlert('Restful table Carbon_User_Sessions failed to execute the update query.', 'danger');
        }
        
        if (!$stmt->rowCount()) {
            throw new PublicAlert('Failed to update the target row.', 'danger');
        }
        
        $argv = array_combine(
            array_map(
                static function($k) { return str_replace('carbon_user_sessions.', '', $k); },
                array_keys($argv)
            ),
            array_values($argv)
        );

        $return = array_merge($return, $argv);

        self::prepostprocessRestRequest($return);
        
        self::postprocessRestRequest($return);
        
        self::completeRest();
        
        return true;
    }

    /**
    * @param array $remove
    * @param string|null $primary
    * @param array $argv
    * @throws PublicAlert
    * @return bool
    */
    public static function Delete(array &$remove, string $primary = null, array $argv = []) : bool
    {
        self::startRest(self::DELETE, $argv);
        
        /** @noinspection SqlWithoutWhere
         * @noinspection UnknownInspectionInspection - intellij is funny sometimes.
         */
        $sql = 'DELETE FROM carbon_user_sessions ';

        $pdo = self::database();
        
        if (null === $primary) {
           /**
            *   While useful, we've decided to disallow full
            *   table deletions through the rest api. For the
            *   n00bs and future self, "I got chu."
            */
            if (empty($argv)) {
                throw new PublicAlert('When deleting from restful tables a primary key or where query must be provided.', 'danger');
            }
            
            $where = self::buildBooleanJoinConditions(self::DELETE, $argv, $pdo);
            
            if (empty($where)) {
                throw new PublicAlert('The where condition provided appears invalid.', 'danger');
            }

            $sql .= ' WHERE ' . $where;
        } else {
            $sql .= ' WHERE  session_id='.self::addInjection($primary, $pdo).'';
        }


        self::jsonSQLReporting(func_get_args(), $sql);

        $stmt = $pdo->prepare($sql);

        self::bind($stmt);

        $r = $stmt->execute();

        if ($r) {
            $remove = [];
        }
        
        self::prepostprocessRestRequest($return);
        
        self::postprocessRestRequest($return);
        
        self::completeRest();
        
        return $r;
    }
     

    
}
