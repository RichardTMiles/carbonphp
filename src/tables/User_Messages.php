<?php

namespace CarbonPHP\Tables;

// Restful defaults
use CarbonPHP\Helpers\RestfulValidations;
use CarbonPHP\Rest;
use CarbonPHP\Interfaces\iRestSinglePrimaryKey;
use PDO;

// Custom User Imports
use CarbonPHP\Database;
use CarbonPHP\Error\PublicAlert;
use JsonException;
use PDOException;
use function array_key_exists;
use function count;
use function func_get_args;
use function is_array;

/**
 *
 * Class User_Messages
 * @package CarbonPHP\Tables
 * @note Note for convenience, a flag '-prefix' maybe passed to remove table prefixes.
 *  Use '-help' for a full list of options.
 * @link https://carbonphp.com/ 
 *
 * This class contains autogenerated code.
 * This class is a 1=1 relation named after a table in the database schema provided to the program `RestBuilder`.
 * Your edits are preserved during updates given they follow::
 *      NEW METHODS SHOULD ONLY BE STATIC and may be reordered during generation.
 *      FUNCTIONS MUST NOT EXIST outside the class. (methods and functions are not the same.)
 *      IMPORTED CLASSED AND FUNCTIONS ARE ALLOWED though maybe reordered.
 *      ADDITIONAL CONSTANTS of any kind ARE NOT ALLOWED.
 *      ADDITIONAL CLASS MEMBER VARIABLES are NOT ALLOWED.
 *
 * When creating static member functions which require persistent variables, consider making them static members of that 
 *  static method.
 */
class User_Messages extends Rest implements iRestSinglePrimaryKey
{
    use RestfulValidations;
    
    public const CLASS_NAME = 'User_Messages';
    
    public const CLASS_NAMESPACE = 'CarbonPHP\Tables\\';
    
    public const TABLE_NAME = 'carbon_user_messages';
    
    public const TABLE_PREFIX = 'carbon_';
    
    public const DIRECTORY = __DIR__ . DIRECTORY_SEPARATOR;
    
    public const VERBOSE_LOGGING = false;
    
    public const QUERY_WITH_DATABASE = true;
    
    public const DATABASE = 'CarbonPHP';
    
    public const JSON_COLUMNS = [];
    
    // Tables we have a foreign key reffrence to
    public const INTERNAL_TABLE_CONSTRAINTS = [
        self::FROM_USER_ID => Carbons::ENTITY_PK,
        self::MESSAGE_ID => Carbons::ENTITY_PK,
        self::TO_USER_ID => Carbons::ENTITY_PK,
    ];
    
    // Tables that reference this tables columns via FK
    public const EXTERNAL_TABLE_CONSTRAINTS = [
    ];

    /** VALIDATE_AFTER_REBUILD
     * If set to true, after running the REFRESH_SCHEMA the sql generated by a mysql dump should match, otherwise an 
     * error will be thrown. Set this to false if the table being generated is 3rd party, such as wordpress internals.
     * [C6] Internal tables will never be validated using restful generated files, outside the library, despite this setting. 
     * @note this constant can be modified and will presist after rebuild.
    **/
    public const VALIDATE_AFTER_REBUILD = true;
    
    /**
     * COLUMNS
     * The columns below are a 1=1 mapping to the columns found in carbon_user_messages. 
     * Changes, such as adding or removing a column, MAY be made first in the database. The ResitBuilder program will 
     * capture any changes made in MySQL and update this file auto-magically. If you work in a team it is RECOMMENDED to
     * programmatically make these changes using the REFRESH_SCHEMA constant below.
    **/
    public const MESSAGE_ID = 'carbon_user_messages.message_id'; 

    public const FROM_USER_ID = 'carbon_user_messages.from_user_id'; 

    public const TO_USER_ID = 'carbon_user_messages.to_user_id'; 

    public const MESSAGE = 'carbon_user_messages.message'; 

    public const MESSAGE_READ = 'carbon_user_messages.message_read'; 

    public const CREATION_DATE = 'carbon_user_messages.creation_date'; 

    /**
     * COLUMNS
     * Interfacing with the restful return can be done using objects which allow your editor to smartly type fields.
     * The referenced return &$return from any Rest::Get method can be directly passed back into its calling classes 
     *  constructor. One might use these fields below with the following ::
     *
     *    public User_Messages $carbon_user_messages;
     *
     * The definition above can be defined with the following ::
     *
     *    $carbon_user_messages = new User_Messages($return);
     *
     * @note this method is unnecessary and should be avoided if not needed for clarity of clean code. 
    **/
    public string $message_id;

    public string $from_user_id;

    public string $to_user_id;

    public string $message;

    public string $message_read;

    public string $creation_date;
    
    /**
     * PRIMARY
     * This could be null for tables without primary key(s), a string for tables with a single primary key, or an array 
     * given composite primary keys. The existence and amount of primary keys of the will also determine the interface 
     * aka method signatures used.
    **/
    public const PRIMARY = 'carbon_user_messages.message_id';

    /**
     * AUTO_INCREMENT_PRIMARY_KEY
     * Post requests will return the new primary key.
     * Caution: auto incrementing columns are considered bad practice in MySQL Sharded system. This is an
     * advanced configuration, so if you don't know what it means you can probably ignore this. CarbonPHP is designed to
     * manage your primary keys through a mysql generated UUID entity system. Consider turning your primary keys into 
     * foreign keys which reference $prefix . 'carbon_carbons.entity_pk'. More on why this is effective at 
     * @link https://www.carbonPHP.com
    **/
    public const AUTO_INCREMENT_PRIMARY_KEY = false;
        
    /**
     * CARBON_CARBONS_PRIMARY_KEY
     * does your table reference $prefix . 'carbon_carbons.entity_pk'
    **/
    public const CARBON_CARBONS_PRIMARY_KEY = true;
    
    /**
     * COLUMNS
     * This is a convenience constant for accessing your data after it has be returned from a rest operation. It is needed
     * as Mysql will strip away the table name we have explicitly provided to each column (to help with join statments).
     * Thus, accessing your return values might look something like:
     *      $return[self::COLUMNS[self::EXAMPLE_COLUMN_ONE]]
    **/ 
    public const COLUMNS = [
        self::MESSAGE_ID => 'message_id',
        self::FROM_USER_ID => 'from_user_id',
        self::TO_USER_ID => 'to_user_id',
        self::MESSAGE => 'message',
        self::MESSAGE_READ => 'message_read',
        self::CREATION_DATE => 'creation_date',
    ];
    
    /**
     * PDO_VALIDATION
     * This is automatically generated. Modify your mysql table directly and rerun RestBuilder to see changes.
    **/
    public const PDO_VALIDATION = [
        self::MESSAGE_ID => [self::MYSQL_TYPE => 'binary', self::PDO_TYPE => PDO::PARAM_STR, self::MAX_LENGTH => '16', self::AUTO_INCREMENT => false, self::SKIP_COLUMN_IN_POST => false],
        self::FROM_USER_ID => [self::MYSQL_TYPE => 'binary', self::PDO_TYPE => PDO::PARAM_STR, self::MAX_LENGTH => '16', self::AUTO_INCREMENT => false, self::SKIP_COLUMN_IN_POST => false],
        self::TO_USER_ID => [self::MYSQL_TYPE => 'binary', self::PDO_TYPE => PDO::PARAM_STR, self::MAX_LENGTH => '16', self::AUTO_INCREMENT => false, self::SKIP_COLUMN_IN_POST => false],
        self::MESSAGE => [self::MYSQL_TYPE => 'text', self::PDO_TYPE => PDO::PARAM_STR, self::MAX_LENGTH => '', self::AUTO_INCREMENT => false, self::SKIP_COLUMN_IN_POST => false],
        self::MESSAGE_READ => [self::MYSQL_TYPE => 'tinyint', self::PDO_TYPE => PDO::PARAM_INT, self::MAX_LENGTH => '1', self::AUTO_INCREMENT => false, self::SKIP_COLUMN_IN_POST => false, self::DEFAULT_POST_VALUE => '0'],
        self::CREATION_DATE => [self::MYSQL_TYPE => 'datetime', self::PDO_TYPE => PDO::PARAM_STR, self::MAX_LENGTH => '', self::AUTO_INCREMENT => false, self::SKIP_COLUMN_IN_POST => true, self::DEFAULT_POST_VALUE => self::CURRENT_TIMESTAMP],
    ];
     
    /**
     * REFRESH_SCHEMA
     * These directives should be designed to maintain and update your team's schema &| database &| table over time. 
     * It is RECOMMENDED that ALL changes you make in your local env be programmatically coded out in callables such as 
     * the 'tableExistsOrExecuteSQL' method call below. If a PDO exception is thrown with `$e->getCode()` equal to 42S02 
     * or 1049 CarbonPHP will attempt to REFRESH the full database with with all directives in all tables. If possible 
     * keep table specific procedures in it's respective restful-class table file. Check out the 'tableExistsOrExecuteSQL' 
     * method in the parent class to see a more abstract procedure.
     * Each directive MUST be designed to run multiple times without failure.
     * @defaults
     *   public const REFRESH_SCHEMA = [
     *      [self::class => 'tableExistsOrExecuteSQL', self::TABLE_NAME, self::TABLE_PREFIX, self::REMOVE_MYSQL_FOREIGN_KEY_CHECKS .
     *                  PHP_EOL . self::CREATE_TABLE_SQL . PHP_EOL . self::REVERT_MYSQL_FOREIGN_KEY_CHECKS, true],
     *      [self::class => 'buildMysqlHistoryTrigger', self::TABLE_NAME]
     *   ];
     *
     */
    public const REFRESH_SCHEMA = [
        [self::class => 'tableExistsOrExecuteSQL', self::TABLE_NAME, self::TABLE_PREFIX, self::REMOVE_MYSQL_FOREIGN_KEY_CHECKS .
                        PHP_EOL . self::CREATE_TABLE_SQL . PHP_EOL . self::REVERT_MYSQL_FOREIGN_KEY_CHECKS, true]
    ];
    
    /**
     * REGEX_VALIDATION
     * Regular Expression validations will run before and recommended over PHP_VALIDATION.
     * It is a 1 to 1 column regex relation with fully regex for preg_match_all(). This regex must satisfy the condition 
     *        1 > preg_match_all(self::$compiled_regex_validations[$column], $value, ...
     * 
     * Table generated column constants must be used. 
     *       self::EXAMPLE_COLUMN_NAME => '#^[A-F0-9]{20,35}$#i'
     *
     * @link https://regexr.com
     * @link https://php.net/manual/en/function.preg-match-all.php
     */
    public const REGEX_VALIDATION = []; 
     
     
    /**
     * PHP_VALIDATION
     * PHP validations works as follows:
     * @note regex validation is always step #1 and should be favored over php validations.
     *  Syntax ::
     *      [Example_Class::class => 'disallowPublicAccess', (optional) ...$rest]
     *      self::EXAMPLE_COLUMN => [Example_Class::class => 'exampleOtherMethod', (optional) ...$rest]
     *
     *  Callables defined above MUST NOT RETURN FALSE. Moreover; return values are ignored so `): void {` may be used. 
     *  array_key_first() must return a fully qualified class namespace. In the example above Example_Class would be a
     *  class defined in our system. PHP's `::class` appended to the end will return the fully qualified namespace. Note
     *  this will require the custom import added to the top of the file. You can allow your editor to add these for you
     *  as the RestBuilder program will capture, preserve, and possibly reorder the imports. The value of the first key 
     *  MUST BE the exact name of a member-method of that class. Typically validations are defined in the same class 
     *  they are used ('self::class') though it is useful to export more dynamic functions. The $rest variable can be 
     *  used to add additional arguments to the request. RESTFUL INTERNAL ARGUMENTS will be passed before any use defined
     *  variables after the first key value pair. Only array values will be passed to the method. Thus, additional keys 
     *  listed in the array will be ignored. Take for example::
     *
     *      [ self::class => 'validateUnique', self::class, self::EXAMPLE_COLUMN]
     *  The above is defined in RestfulValidations::class. 
     *      RestfulValidations::validateUnique(string $columnValue, string $className, string $columnName)
     *  Its definition is with a trait this classes inherits using `use` just after the `class` keyword. 
     * 
     *   What is the RESTFUL lifecycle?
     *      Regex validations are done first on any main query; sub-queries are treated like callbacks which get run 
     *      during the main queries invocation. The main query is 'paused' while the sub-query will compile and validate.
     *      Validations across tables are concatenated on joins and sub-queries. All callbacks will be run across any 
     *       table joins.
     *      
     *   What are the RESTFUL INTERNAL ARGUMENTS? (The single $arg string or array passed before my own...)
     *      REST_REQUEST_PREPROCESS_CALLBACKS ::   
     *           PREPROCESS::
     *              Methods defined here will be called at the beginning of every request. 
     *              Each method will be passed ( & self::$REST_REQUEST_PARAMETERS ) by reference so changes can be made pre-request.
     *              Method validations under the main 'PREPROCESS' key will be run first, while validations specific to 
     *              ( GET | POST | PUT | DELETE )::PREPROCESS will be run directly after.
     *
     *           FINAL:: 
     *              Each method will be passed the final ( & $SQL ), which may be a sub-query, by reference.
     *              Modifying the SQL string will effect the parent function. This can have disastrous effects.
     *
     *           COLUMN::
     *              Preformed while a column is being parsed in a query. The first column validations to run.
     *              Each column specific method under PREPROCESS will be passed nothing from rest. 
     *              Each method will ONLY be RUN ONCE regardless of how many times the column has been seen. 
     *
     *      COLUMN::
     *           Column validations are only run when they have been parsed in the query. Global column validations maybe
     *            RUN MULTIPLE TIMES if the column is used multiple times in a single restful query. 
     *           If you have a column that is used multiple times the validations will run for each occurrence.
     *           Column validation can mean many thing. There are three possible scenarios in which your method 
     *            signature would change. For this reason it is more common to use method ( GET | POST ... ) wise column validations.
     *              *The signature required are as follows:
     *                  Should the column be...
     *                      SELECTED:  
     *                          In a select stmt no additional parameters will be passed.
     *                      
     *                      ORDERED BY: (self::ASC | self::DESC)
     *                          The $operator will be passed to the method.
     *  
     *                      JOIN STMT:
     *                          The $operator followed by the $value will be passed. 
     *                          The operator could be :: >,<,<=,<,=,<>,=,<=>
     *
     *      REST_REQUEST_FINNISH_CALLBACKS::
     *          PREPROCESS::
     *              These callbacks are called after a successful PDOStatement->execute() but before Database::commit().
     *              Each method will be passed ( GET => &$return, DELETE => &$remove, PUT => &$returnUpdated ) by reference. 
     *              POST will BE PASSED NULL.          
     *
     *          FINAL::
     *              Run directly after method specific [FINAL] callbacks.
     *              The final, 'final' callback set. After these run rest will return. 
     *              Each method will be passed ( GET => &$return, DELETE => &$remove, PUT => &$returnUpdated ) by reference. 
     *              POST will BE PASSED NULL. 
     *
     *          COLUMN::
     *              These callables will be run after the [( GET | POST | PUT | DELETE )][FINAL] methods.
     *              Directly after, the [REST_REQUEST_FINNISH_CALLBACKS][FINAL] will run. 
     *              
     *
     *      (POST|GET|PUT|DELETE)::
     *          PREPROCESS::
     *              Methods run after any root 'REST_REQUEST_PREPROCESS_CALLBACKS'
     *              Each method will not be passed any argument from system. User arguments will be directly reflected.
     *
     *          COLUMN::
     *              Methods run after any root column validations, the last of the PREPROCESS column validations to run.
     *              Based on the existences and number of primary key(s), the signature will change. 
     *               See the notes on the base column validations as signature of parameters may change. 
     *              It is not possible to directly define a method->column specific post processes. This can be done by
     *               dynamically pairing multiple method processes starting with one here which signals a code routine 
     *               in a `finial`-ly defined method. The FINAL block specific to the method would suffice. 
     *
     *          FINAL::
     *              Passed the ( & $return )  
     *              Run before any other column validation 
     *
     *  Be aware the const: self::DISALLOW_PUBLIC_ACCESS = [self::class => 'disallowPublicAccess'];
     *  could be used to replace each occurrence of 
     *          [self::class => 'disallowPublicAccess', self::class]
     *  though would loose information as self::class is a dynamic variable which must be used in this class given 
     *  static and constant context. 
     *  @default   
     *      public const PHP_VALIDATION = [ 
     *          self::REST_REQUEST_PREPROCESS_CALLBACKS => [ 
     *              self::PREPROCESS => [ 
     *                  [self::class => 'disallowPublicAccess', self::class],
     *              ]
     *          ],
     *          self::GET => [ 
     *              self::PREPROCESS => [ 
     *                  [self::class => 'disallowPublicAccess', self::class],
     *              ],
     *              self::MESSAGE_ID => [
     *                  [self::class => 'disallowPublicAccess', self::MESSAGE_ID]
     *              ],
     *              self::FROM_USER_ID => [
     *                  [self::class => 'disallowPublicAccess', self::FROM_USER_ID]
     *              ],
     *              self::TO_USER_ID => [
     *                  [self::class => 'disallowPublicAccess', self::TO_USER_ID]
     *              ],
     *              self::MESSAGE => [
     *                  [self::class => 'disallowPublicAccess', self::MESSAGE]
     *              ],
     *              self::MESSAGE_READ => [
     *                  [self::class => 'disallowPublicAccess', self::MESSAGE_READ]
     *              ],
     *              self::CREATION_DATE => [
     *                  [self::class => 'disallowPublicAccess', self::CREATION_DATE]
     *              ],
     *          ],    
     *          self::POST => [ self::PREPROCESS => [[ self::class => 'disallowPublicAccess', self::class ]]],    
     *          self::PUT => [ self::PREPROCESS => [[ self::class => 'disallowPublicAccess', self::class ]]],    
     *          self::DELETE => [ self::PREPROCESS => [[ self::class => 'disallowPublicAccess', self::class ]]],
     *          self::REST_REQUEST_FINNISH_CALLBACKS => [ self::PREPROCESS => [[ self::class => 'disallowPublicAccess', self::class ]]]    
     *      ];
     *  @note you can remove the constant entirely and re-run rest builder to reset the default.
     *  @version ^9
     */ 
    public const PHP_VALIDATION = [ 
        self::REST_REQUEST_PREPROCESS_CALLBACKS => [ 
            self::PREPROCESS => [ 
                [self::class => 'disallowPublicAccess', self::class],
            ]
        ],
        self::GET => [ 
            self::PREPROCESS => [ 
                [self::class => 'disallowPublicAccess', self::class],
            ],
            self::MESSAGE_ID => [
                [self::class => 'disallowPublicAccess', self::MESSAGE_ID]
            ],
            self::FROM_USER_ID => [
                [self::class => 'disallowPublicAccess', self::FROM_USER_ID]
            ],
            self::TO_USER_ID => [
                [self::class => 'disallowPublicAccess', self::TO_USER_ID]
            ],
            self::MESSAGE => [
                [self::class => 'disallowPublicAccess', self::MESSAGE]
            ],
            self::MESSAGE_READ => [
                [self::class => 'disallowPublicAccess', self::MESSAGE_READ]
            ],
            self::CREATION_DATE => [
                [self::class => 'disallowPublicAccess', self::CREATION_DATE]
            ],
            
        ],    
        self::POST => [ self::PREPROCESS => [[ self::class => 'disallowPublicAccess', self::class ]]],    
        self::PUT => [ self::PREPROCESS => [[ self::class => 'disallowPublicAccess', self::class ]]],    
        self::DELETE => [ self::PREPROCESS => [[ self::class => 'disallowPublicAccess', self::class ]]],
        self::REST_REQUEST_FINNISH_CALLBACKS => [ self::PREPROCESS => [[ self::class => 'disallowPublicAccess', self::class ]]]    
    ]; 
   
    /**
     * CREATE_TABLE_SQL is autogenerated and should not be manually updated. Make changes in MySQL and regenerate using
     * the RestBuilder program.
     */
    public const CREATE_TABLE_SQL = /** @lang MySQL */ <<<MYSQL
    CREATE TABLE `carbon_user_messages` (
  `message_id` binary(16) NOT NULL,
  `from_user_id` binary(16) NOT NULL,
  `to_user_id` binary(16) NOT NULL,
  `message` text COLLATE utf8mb4_unicode_520_ci NOT NULL,
  `message_read` tinyint(1) DEFAULT '0',
  `creation_date` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`message_id`),
  KEY `messages_entity_entity_pk_fk` (`message_id`),
  KEY `messages_entity_user_from_pk_fk` (`to_user_id`),
  KEY `carbon_user_messages_carbon_entity_pk_fk` (`from_user_id`),
  CONSTRAINT `carbon_user_messages_carbon_entity_pk_fk` FOREIGN KEY (`from_user_id`) REFERENCES `carbon_carbons` (`entity_pk`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `messages_entity_entity_pk_fk` FOREIGN KEY (`message_id`) REFERENCES `carbon_carbons` (`entity_pk`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `messages_entity_user_from_pk_fk` FOREIGN KEY (`to_user_id`) REFERENCES `carbon_carbons` (`entity_pk`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_520_ci;
MYSQL;
   

    
   /**
    * Currently nested aggregation is not supported. It is recommended to avoid using 'AS' where possible. Sub-selects are 
    * allowed and do support 'as' aggregation. Refer to the static subSelect method parameters in the parent `Rest` class.
    * All supported aggregation is listed in the example below. Note while the WHERE and JOIN members are syntactically 
    * similar, and are moreover compiled through the same method, our aggregation is not. Please refer to this example 
    * when building your queries. By design, queries using subSelect are only allowed internally. Public Sub-Selects may 
    * be given an optional argument with future releases but will never default to on. Thus, you external API validation
    * need only validate for possible table joins. In many cases sub-selects can be replaces using simple joins, this is
    * highly recommended.
    *
    *   $argv = [
    *       Rest::SELECT => [
    *              'table_name.column_name',                            // bad, dont pass strings manually. Use Table Constants instead.
    *              self::EXAMPLE_COLUMN_ONE,                            // good, 
    *              [self::EXAMPLE_COLUMN_TWO, Rest::AS, 'customName'],
    *              [Rest::COUNT, self::EXAMPLE_COLUMN_TWO, 'custom_return_name_using_as'],
    *              [Rest::GROUP_CONCAT, self::EXAMPLE_COLUMN_THREE], 
    *              [Rest::MAX, self::EXAMPLE_COLUMN_FOUR], 
    *              [Rest::MIN, self::EXAMPLE_COLUMN_FIVE], 
    *              [Rest::SUM, self::EXAMPLE_COLUMN_SIX], 
    *              [Rest::DISTINCT, self::EXAMPLE_COLUMN_SEVEN], 
    *              ANOTHER_EXAMPLE_TABLE::subSelect($primary, $argv, $as, $pdo, $database)
    *       ],
    *       Rest::WHERE => [
    *              
    *              self::EXAMPLE_COLUMN_NINE => 'Value To Constrain',                       // self::EXAMPLE_COLUMN_NINE AND           
    *              'Defaults to boolean AND grouping' => 'Nesting array switches to OR',    // ''='' AND 
    *              [
    *                  'Column Name' => 'Value To Constrain',                                  // ''='' OR
    *                  'This array is OR'ed together' => 'Another sud array would `AND`'       // ''=''
    *                  [ etc... ]
    *              ],
    *              'last' => 'whereExample'                                                  // AND '' = ''
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
    *                    [ 'Column Name', Rest::LESS_THAN, 'Another Column Name']           // NOTE the Rest::LESS_THAN
    *                ]
    *            ],
    *            Rest::LEFT_OUTER => [
    *                Example_Table::CLASS_NAME => [
    *                    Location::USER_ID => Users::ID,
    *                    Location_References::ENTITY_KEY => $custom_var,
    *                   
    *                ],
    *                Example_Table_Two::CLASS_NAME => [
    *                    Example_Table_Two::ID => Example_Table_Two::subSelect($primary, $argv, $as, $pdo, $database)
    *                    ect... 
    *                ]
    *            ]
    *        ],
    *        Rest::PAGINATION => [
    *              Rest::PAGE => (int) 0, // used for pagination which equates to 
    *                  // ... LIMIT ' . (($argv[self::PAGINATION][self::PAGE] - 1) * $argv[self::PAGINATION][self::LIMIT]) 
    *                  //       . ',' . $argv[self::PAGINATION][self::LIMIT];
    *              
    *              Rest::LIMIT => (int) 90, // The maximum number of rows to return,
    *                       setting the limit explicitly to 1 will return a key pair array of only the
    *                       singular result. SETTING THE LIMIT TO NULL WILL ALLOW INFINITE RESULTS (NO LIMIT).
    *                       The limit defaults to 100 by design.
    *
    *               Rest::ORDER => [self::EXAMPLE_COLUMN_TEN => Rest::ASC ],  // i.e.  'username' => Rest::DESC
    *         ],
    *   ];
    *
    *
    * @param array $return
    * @param string|null $primary
    * @param array $argv
    * @generated
    * @return bool
    */
    public static function get(array &$return, string $primary = null, array $argv = []): bool
    {
        return self::select($return, $argv, $primary === null ? null : [ self::PRIMARY => $primary ]);
    }

    /**
     * @param array $data 
     * @return bool|string|mixed
     * @generated
     */
    public static function post(array &$post = [])
    {   
        return self::insert($post);
    }
    
    /**
    * 
    * 
    * Tables where primary keys exist must be updated by its primary key. 
    * Column should be in a key value pair passed to $argv or optionally using syntax:
    * $argv = [
    *       Rest::UPDATE => [
    *              ...
    *       ]
    * ]
    * 
    * @param array $returnUpdated - will be merged with with array_merge, with a successful update. 
    * @param string|null $primary
    * @param array $argv 
    * @generated
    * @return bool - if execute fails, false will be returned and $returnUpdated = $stmt->errorInfo(); 
    */
    public static function put(array &$returnUpdated, string $primary = null, array $argv = []) : bool
    {
        return self::updateReplace($returnUpdated, $argv, $primary === null ? null : [ self::PRIMARY => $primary ]);
    }

    /**
    * @param array $remove
    * @param string|null $primary
    * @param array $argv
    * @generated
    * @return bool
    */
    public static function delete(array &$remove, string $primary = null, array $argv = []) : bool
    {
        return self::remove($remove, $argv, $primary === null ? null : [ self::PRIMARY => $primary ]);
    }
}
