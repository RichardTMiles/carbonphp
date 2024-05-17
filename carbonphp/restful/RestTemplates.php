<?php

namespace CarbonPHP\Restful;


class RestTemplates
{


    public static function parseSQLForTemplate($sql) {
        $pattern = '/CREATE\s+TABLE\s+`?(\w+)`?\s+\(((.|\n)+?)\)\s*(ENGINE=.+?);/m';
        preg_match_all($pattern, $sql, $tableMatches, PREG_SET_ORDER);

        $tableData = [];
        $references = [];

        foreach ($tableMatches as $tableMatch) {
            $tableName = $tableMatch[1];
            $columnDefinitions = $tableMatch[2];

            $columns = [];
            $columnRegex = '/\s*`([^`]*)`\s+(\w+)(?:\(([^)]+)\))?\s*(NOT NULL)?\s*(AUTO_INCREMENT)?\s*(DEFAULT\s+\'[^\']*\'|DEFAULT\s+\S+)?/m';

            preg_match_all($columnRegex, $columnDefinitions, $columnMatches, PREG_SET_ORDER);
            foreach ($columnMatches as $match) {
                $columns[$match[1]] = [
                    'type' => $match[2],
                    'length' => $match[3] ?? '',
                    'notNull' => !empty($match[4]),
                    'autoIncrement' => !empty($match[5]),
                    'defaultValue' => $match[6] ?? ''
                ];
            }

            $primaryKeyRegex = '/PRIMARY KEY \(([^)]+)\)/i';
            preg_match($primaryKeyRegex, $columnDefinitions, $primaryKeyMatch);
            $primaryKeys = $primaryKeyMatch ? array_map(function($key) { return trim($key, '`'); }, explode(',', $primaryKeyMatch[1])) : [];

            $foreignKeyRegex = '/CONSTRAINT `([^`]+)` FOREIGN KEY \(`([^`]+)`\) REFERENCES `([^`]+)` \(`([^`]+)`\)( ON DELETE (\w+))?( ON UPDATE (\w+))?/';
            preg_match_all($foreignKeyRegex, $columnDefinitions, $foreignKeyMatches, PREG_SET_ORDER);

            foreach ($foreignKeyMatches as $foreignKeyMatch) {
                $references[] = [
                    'TABLE' => $tableName,
                    'CONSTRAINT' => $foreignKeyMatch[1],
                    'FOREIGN_KEY' => $foreignKeyMatch[2],
                    'REFERENCES' => $foreignKeyMatch[3] . '.' . $foreignKeyMatch[4],
                    'ON_DELETE' => $foreignKeyMatch[6] ?? null,
                    'ON_UPDATE' => $foreignKeyMatch[8] ?? null
                ];
            }

            $tableData[$tableName] = [
                'TABLE_NAME' => $tableName,
                'COLUMNS' => $columns,
                'PRIMARY_KEYS' => $primaryKeys,
                // Additional fields would be added here, similar to TypeScript version
            ];
        }

        return $tableData;
    }

    public static function restTrait(): string
    {

        return /** @lang Handlebars */ <<<STRING
<?php

namespace {{namespace}}\Traits;

trait {{ucEachTableName}}_Columns
{
    
    /**
     * COLUMNS
     * Interfacing with the restful return can be done using objects which allow your editor to smartly type fields.
     * The referenced return &\$return from any Rest::Get method can be directly passed back into its calling classes 
     *  constructor. One might use these fields below with the following ::
     *
     *    public {{ucEachTableName}} \${{TableName}};
     *
     * The definition above can be defined with the following ::
     *
     *    \${{TableName}} = new {{ucEachTableName}}(\$return);
     *
     * @note this method is unnecessary and should be avoided if not needed for clarity of clean code. 
    **/{{#explode}}
    public ?{{#json}}array{{/json}}{{^json}}{{phpType}}{{/json}} \${{name}};
    {{/explode}}

}


STRING;


    }


    public static function restTemplate(): string
    {

        return /** @lang Handlebars */ <<<STRING
<?php

namespace {{namespace}};

// Restful defaults
use CarbonPHP\Interfaces\{{#primaryExists}}{{#multiplePrimary}}iRestMultiplePrimaryKeys{{/multiplePrimary}}{{^multiplePrimary}}iRestSinglePrimaryKey{{/multiplePrimary}}{{/primaryExists}}{{^primaryExists}}iRestNoPrimaryKey{{/primaryExists}};
use {{namespace}}\Traits\{{ucEachTableName}}_Columns;
{{staticNamespaces}}

// Custom User Imports
{{#CustomImports}}{{{CustomImports}}}{{/CustomImports}}

/**
 *
 * Class {{ucEachTableName}}
 * @package {{namespace}}
 * @note Note for convenience, a flag '-prefix' maybe passed to remove table prefixes.
 *  Use '-help' for a full list of options.
 * @link https://carbonphp.com/ 
 *
 * This class contains autogenerated code.
 * This class is a 1=1 relation named after a table in the database schema provided to the program `RestBuilder`.
 * Your edits are preserved during updates given they follow::
 *      NEW METHODS SHOULD ONLY BE PUBLIC AND STATIC MEMBERS and may be reordered during generation.
 *      FUNCTIONS MUST NOT EXIST outside the class. (methods and functions are not the same.)
 *      IMPORTED CLASSED AND FUNCTIONS ARE ALLOWED though maybe reordered.
 *      ADDITIONAL CONSTANTS of any kind ARE NOT ALLOWED.
 *      ADDITIONAL CLASS MEMBER VARIABLES are NOT ALLOWED.
 *
 * When creating static member functions which require persistent variables, consider making them static members of that 
 *  static method.
 */
class {{ucEachTableName}} extends Rest implements {{#primaryExists}}{{#multiplePrimary}}iRestMultiplePrimaryKeys{{/multiplePrimary}}{{^multiplePrimary}}iRestSinglePrimaryKey{{/multiplePrimary}}{{/primaryExists}}{{^primaryExists}}iRestNoPrimaryKey{{/primaryExists}}
{
    use {{ucEachTableName}}_Columns;
    
    public const CLASS_NAME = '{{ucEachTableName}}';
    
    public const CLASS_NAMESPACE = '{{namespace}}\\\\';
    
    public const TABLE_NAME = '{{TableName}}';
    
    public const TABLE_PREFIX = {{#prefixReplaced}}'{{prefix}}'{{/prefixReplaced}}{{^prefixReplaced}}''{{/prefixReplaced}};
    
    public const DIRECTORY = __DIR__ . DIRECTORY_SEPARATOR;
    
    public const VERBOSE_LOGGING = false;
    
    public const QUERY_WITH_DATABASE = {{#QueryWithDatabaseName}}true{{/QueryWithDatabaseName}}{{^QueryWithDatabaseName}}false{{/QueryWithDatabaseName}};
    
    public const DATABASE = '{{#QueryWithDatabaseName}}{{database}}{{/QueryWithDatabaseName}}';
    
    public const JSON_COLUMNS = [{{#explode}}{{#json}}'{{name}}',{{/json}}{{/explode}}];

    public const AUTO_ESCAPE_POST_HTML_SPECIAL_CHARS = {{#autoEscape}}{{autoEscape}}{{/autoEscape}}{{^autoEscape}}false{{/autoEscape}};
    
    // Tables we have a foreign key reference to
    public const INTERNAL_TABLE_CONSTRAINTS = [{{#TABLE_CONSTRAINTS}}
        {{key}} => {{references}},{{/TABLE_CONSTRAINTS}}
    ];
    
    // Tables that reference this tables columns via FK
    public const EXTERNAL_TABLE_CONSTRAINTS = [{{#EXTERNAL_TABLE_CONSTRAINTS}}
        {{key}} => {{references}},{{/EXTERNAL_TABLE_CONSTRAINTS}}
    ];

    /** VALIDATE_AFTER_REBUILD
     * If set to true, after running the REFRESH_SCHEMA the sql generated by a mysql dump should match, otherwise an 
     * error will be thrown. Set this to false if the table being generated is 3rd party, such as wordpress internals.
     * [C6] Internal tables will never be validated using restful generated files, outside the library, despite this setting. 
     * @note this constant can be modified and will persist after rebuild.
    **/{{#DONT_VALIDATE_AFTER_REBUILD}}
    public const VALIDATE_AFTER_REBUILD = false;
    {{/DONT_VALIDATE_AFTER_REBUILD}}{{^DONT_VALIDATE_AFTER_REBUILD}}
    public const VALIDATE_AFTER_REBUILD = true;
    {{/DONT_VALIDATE_AFTER_REBUILD}}
 
    
    /**
     * PRIMARY
     * This could be null for tables without primary key(s), a string for tables with a single primary key, or an array 
     * given composite primary keys. The existence and amount of primary keys of the will also determine the interface 
     * aka method signatures used.
    **/
    public const PRIMARY = {{^primaryExists}}null{{/primaryExists}}{{#primaryExists}}{{#multiplePrimary}}[{{#primary}}{{#name}}'{{TableName}}.{{name}}',{{/name}}{{/primary}}
    ]{{/multiplePrimary}}{{^multiplePrimary}}{{#primary}}'{{TableName}}.{{name}}'{{/primary}}{{/multiplePrimary}}{{/primaryExists}};

    /**
     * AUTO_INCREMENT_PRIMARY_KEY
     * Post requests will return the new primary key.
     * Caution: auto incrementing columns are considered bad practice in MySQL Sharded system. This is an
     * advanced configuration, so if you don't know what it means you can probably ignore this. CarbonPHP is designed to
     * manage your primary keys through a mysql generated UUID entity system. Consider turning your primary keys into 
     * foreign keys which reference \$prefix . 'carbon_carbons.entity_pk'. More on why this is effective at 
     * @link https://www.carbonPHP.com
    **/
    public const AUTO_INCREMENT_PRIMARY_KEY = {{#auto_increment_return_key}}true{{/auto_increment_return_key}}{{^auto_increment_return_key}}false{{/auto_increment_return_key}};
        
    /**
     * CARBON_CARBONS_PRIMARY_KEY
     * does your table reference \$prefix . 'carbon_carbons.entity_pk'
    **/
    public const CARBON_CARBONS_PRIMARY_KEY = {{#CARBON_CARBONS_PRIMARY_KEY}}true{{/CARBON_CARBONS_PRIMARY_KEY}}{{^CARBON_CARBONS_PRIMARY_KEY}}false{{/CARBON_CARBONS_PRIMARY_KEY}};
    
    /**
     * COLUMNS
     * The columns below are a 1=1 mapping to the columns found in {{TableName}}. 
     * Changes, such as adding or removing a column, MAY be made first in the database. The ResitBuilder program will 
     * capture any changes made in MySQL and update this file auto-magically. If you work in a team it is RECOMMENDED to
     * programmatically make these changes using the REFRESH_SCHEMA constant below.
    **/{{#explode}}
    public const {{caps}} = '{{TableName}}.{{name}}'; 
    {{/explode}}
    
    /**
     * COLUMNS
     * This is a convenience constant for accessing your data after it has be returned from a rest operation. It is needed
     * as Mysql will strip away the table name we have explicitly provided to each column (to help with join statments).
     * Thus, accessing your return values might look something like:
     *      \$return[self::COLUMNS[self::EXAMPLE_COLUMN_ONE]]
    **/ 
    public const COLUMNS = [{{#explode}}
        self::{{caps}} => '{{name}}',{{/explode}}
    ];
    
    /**
     * PDO_VALIDATION
     * This is automatically generated. Modify your mysql table directly and rerun RestBuilder to see changes.
    **/
    public const PDO_VALIDATION = [{{#explode}}
        self::{{caps}} => [ self::MYSQL_TYPE => '{{mysql_type}}', self::NOT_NULL => {{#NOT_NULL}}true{{/NOT_NULL}}{{^NOT_NULL}}false{{/NOT_NULL}}, self::COLUMN_CONSTRAINTS => [{{#COLUMN_CONSTRAINTS}}{{key}} => [ self::CONSTRAINT_NAME => '{{CONSTRAINT_NAME}}', self::UPDATE_RULE => {{UPDATE_RULE}}, self::DELETE_RULE => {{DELETE_RULE}}],{{/COLUMN_CONSTRAINTS}}], self::PDO_TYPE => {{type}}, self::MAX_LENGTH => '{{length}}', self::AUTO_INCREMENT => {{#auto_increment}}true{{/auto_increment}}{{^auto_increment}}false{{/auto_increment}}, self::SKIP_COLUMN_IN_POST => {{#skip}}true{{/skip}}{{^skip}}false{{/skip}}{{#default}}, self::DEFAULT_POST_VALUE => {{#CURRENT_TIMESTAMP}}self::CURRENT_TIMESTAMP{{/CURRENT_TIMESTAMP}}{{^CURRENT_TIMESTAMP}}{{{default}}}{{/CURRENT_TIMESTAMP}}{{/default}}{{#COMMENT}}, self::COMMENT => {{COMMENT}}{{/COMMENT}} ],{{/explode}}
    ];
     
    /**
     * REFRESH_SCHEMA
     * These directives should be designed to maintain and update your team's schema &| database &| table over time. 
     * It is RECOMMENDED that ALL changes you make in your local env be programmatically coded out in callables such as 
     * the 'tableExistsOrExecuteSQL' method call below. If a PDO exception is thrown with `\$e->getCode()` equal to 42S02 
     * or 1049 CarbonPHP will attempt to REFRESH the full database with with all directives in all tables. If possible 
     * keep table specific procedures in it's respective restful-class table file. Check out the 'tableExistsOrExecuteSQL' 
     * method in the parent class to see a more abstract procedure.
     * Each directive MUST be designed to run multiple times without failure.
     * @defaults
     *   public const REFRESH_SCHEMA = [
     *        [self::class => 'buildMysqlHistoryTrigger', self::class]
     *   ];
     *
     *
     * Hint: the following may be uncommented and used to allow explicitly referencing methods with callbacks. No 
     * parameters will be passed to the callbacks. The referencing style above will also be respected in this array. The
     * example callables maybe removed. 
     *
     *    public function __construct(array &\$return = [])
     *    {
     *        parent::__construct(\$return);
     *        
     *        # always create the column in your local database first, re-run the table builder, then add the needed functions
     *        \$this->REFRESH_SCHEMA = [
     *            static fn() => self::execute('ALTER TABLE mytbl ALTER j SET DEFAULT 1000;'),
     *            static fn() => self::execute('ALTER TABLE mytbl ALTER k DROP DEFAULT;'),
     *            static fn() => self::buildMysqlHistoryTrigger(self::TABLE_NAME),
     *            static fn() => self::columnExistsOrExecuteSQL(self::COLUMNS[self::MODIFIED], self::class,
     *                  'alter table '.self::TABLE_NAME.' add '.self::COLUMNS[self::MODIFIED].' DATETIME default CURRENT_TIMESTAMP;'),
     *            static fn() => self::columnIsTypeOrChange(self::COLUMNS[self::MODIFIED], self::class, self::PDO_VALIDATION[self::MODIFIED][self::MYSQL_TYPE]),
     *        ];
     *    }
     *
     * @note columnExistsOrExecuteSQL and columnIsTypeOrChange are both automatically generated and process in the 
     * background during a database refresh. You do not need to add them to your REFRESH_SCHEMA array. You can use them 
     * in complex use cases such as data type manipulation as a reference for your own custom directives.
     *
    **/{{^REFRESH_SCHEMA_PUBLIC}}
    public array \$REFRESH_SCHEMA = [];{{/REFRESH_SCHEMA_PUBLIC}}
     {{#REFRESH_SCHEMA_PUBLIC}}{{{REFRESH_SCHEMA_PUBLIC}}}{{/REFRESH_SCHEMA_PUBLIC}}
     {{^REFRESH_SCHEMA}}
    public const REFRESH_SCHEMA = [
        // [self::class => 'buildMysqlHistoryTrigger', self::class] // experimental
    ];{{/REFRESH_SCHEMA}}{{#REFRESH_SCHEMA}}
    {{{REFRESH_SCHEMA}}}{{/REFRESH_SCHEMA}}
    
    {{^constructorDefined}}
    public function __construct(array &\$return = [])
    {
        parent::__construct(\$return);
         
        # always create the column in your local database first, re-run the table builder, then add the needed functions
        \$this->REFRESH_SCHEMA = [
            
        ];
         
        
        \$this->PHP_VALIDATION = RestfulValidations::getDefaultRestAccess(self::class, [ 
            self::COLUMN => [
               self::GLOBAL_COLUMN_VALIDATION => []
            ],
            self::REST_REQUEST_PREPROCESS_CALLBACKS => [ 
                self::PREPROCESS => [
                    // before any other processing is done, this is the first callback to be executed
                    // typically used to validate the full request, add additional data to the request, and even creating a history log
                    static fn() => self::disallowPublicAccess(self::class)
                ],
                self::FINISH => [
                    // the compiled sql is passed to the callback, the statement has not been executed yet
                ]  
            ],
            self::GET => [
                self::PREPROCESS => [
                   static fn() => self::disallowPublicAccess(self::class)
               ]
            ],
            self::POST => [
                self::PREPROCESS => [
                   static fn() => self::disallowPublicAccess(self::class)
                ]
            ],
            self::PUT => [
                self::PREPROCESS => [
                    static fn() => self::disallowPublicAccess(self::class)
                ]
            ],
            self::DELETE => [
                self::PREPROCESS => [
                    static fn() => self::disallowPublicAccess(self::class)
                ]
            ],
            self::FINISH => [
                self::PREPROCESS => [
                    // Has executed but not committed to the database, id is passed
                ],
                self::FINISH => [
                    // Has executed and committed to the database, results are passed by reference
                ],
            ]
        ]);
    }
     {{/constructorDefined}}
    
    /** Custom User Methods Are Placed Here **/
    
    
{{{custom_methods}}}
   
    /**
     * REGEX_VALIDATION
     * Regular Expression validations will run before and recommended over PHP_VALIDATION.
     * It is a 1 to 1 column regex relation with fully regex for preg_match_all(). This regex must satisfy the condition 
     *        1 > preg_match_all(self::\$compiled_regex_validations[\$column], \$value, ...
     * 
     * Table generated column constants must be used. 
     *       self::EXAMPLE_COLUMN_NAME => '#^[A-F0-9]{20,35}$#i'
     *
     * @link https://regexr.com
     * @link https://php.net/manual/en/function.preg-match-all.php
     */{{^regex_validation}}
    public const REGEX_VALIDATION = [];{{/regex_validation}}{{#regex_validation}}
    {{{regex_validation}}} 
    {{/regex_validation}}     
      
    /**
     * PHP_VALIDATION
     * PHP validations works as follows:
     * @note regex validation is always step #1 and should be favored over php validations.
     *  Syntax ::
     *      [Example_Class::class => 'disallowPublicAccess', (optional) ...\$rest]
     *      self::EXAMPLE_COLUMN => [Example_Class::class => 'exampleOtherMethod', (optional) ...\$rest]
     *
     *  Callables defined above MUST NOT RETURN FALSE. Moreover, return values are ignored so `): void {` may be used. 
     *  array_key_first() must return a fully qualified class namespace. In the example above Example_Class would be a
     *  class defined in our system. PHP's `::class` appended to the end will return the fully qualified namespace. Note
     *  this will require the custom import added to the top of the file. You can allow your editor to add these for you
     *  as the RestBuilder program will capture, preserve, and possibly reorder the imports. The value of the first key 
     *  MUST BE the exact name of a member-method of that class. Typically validations are defined in the same class 
     *  they are used ('self::class') though it is useful to export more dynamic functions. The \$rest variable can be 
     *  used to add additional arguments to the request. RESTFUL INTERNAL ARGUMENTS will be passed before any use defined
     *  variables after the first key value pair. Only array values will be passed to the method. Thus, additional keys 
     *  listed in the array will be ignored. Take for example::
     *
     *      [ RestfulValidations::class => 'validateUnique', self::class, self::EXAMPLE_COLUMN]
     *  The above is defined in RestfulValidations::class. 
     *      RestfulValidations::validateUnique(string \$columnValue, string \$className, string \$columnName)
     *  Its definition is with a trait this classes inherits using `use` just after the `class` keyword. 
     * 
     *   What is the RESTFUL lifecycle?
     *      Regex validations are done first on any main query; sub-queries are treated like callbacks which get run 
     *      during the main queries invocation. The main query is 'paused' while the sub-query will compile and validate.
     *      Validations across tables are concatenated on joins and sub-queries. All callbacks will be run across any 
     *       table joins.
     *      
     *   What are the RESTFUL INTERNAL ARGUMENTS? (The single \$arg string or array passed before my own...)
     *      REST_REQUEST_PREPROCESS_CALLBACKS ::   
     *           PREPROCESS::
     *              Methods defined here will be called at the beginning of every request. 
     *              Each method will be passed ( & self::\$REST_REQUEST_PARAMETERS ) by reference so changes can be made pre-request.
     *              Method validations under the main 'PREPROCESS' key will be run first, while validations specific to 
     *              ( GET | POST | PUT | DELETE )::PREPROCESS will be run directly after.
     *
     *           FINAL:: 
     *              Each method will be passed the final ( & \$SQL ), which may be a sub-query, by reference.
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
     *                          The \$operator will be passed to the method.
     *  
     *                      JOIN STMT:
     *                          The \$operator followed by the \$value will be passed. 
     *                          The operator could be :: >,<,<=,<,=,<>,=,<=>
     *
     *      REST_REQUEST_FINNISH_CALLBACKS::
     *          PREPROCESS::
     *          PRECOMMIT::
     *              These callbacks are called after a successful PDOStatement->execute() but before Database::commit().
     *              Each method will be passed ( POST => void|&\$returnID, DELETE => &\$remove, PUT => &\$returnUpdated ) by reference. 
     *              POST will BE PASSED NULL.          
     *
     *          FINAL::
     *              Run directly after method specific [FINAL] callbacks.
     *              The final, 'final' callback set. After these run rest will return. 
     *              Each method will be passed ( GET => void|&\$returnID, DELETE => &\$remove, PUT => &\$returnUpdated ) by reference. 
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
     *          PRECOMMIT::
     *              These callbacks are called after a successful PDOStatement->execute() but before Database::commit().
     *              Each method will be passed ( POST => void|&\$returnID, DELETE => &\$remove, PUT => &\$returnUpdated ) by reference. 
     *              POST will BE PASSED NULL.
     *
     *          FINAL::
     *              Passed the ( & \$return )  
     *              Run before any other column validation 
     *
     *  Be aware the const: self::DISALLOW_PUBLIC_ACCESS = [self::class => 'disallowPublicAccess'];
     *  could be used to replace each occurrence of 
     *          [self::class => 'disallowPublicAccess', self::class]
     *  though would lose information as self::class is a dynamic variable which must be used in this class given 
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
     *              ],{{#explode}}
     *              self::{{caps}} => [
     *                  [self::class => 'disallowPublicAccess', self::{{caps}}]
     *              ],{{/explode}}
     *          ],    
     *          self::POST => [ self::PREPROCESS => [[ self::class => 'disallowPublicAccess', self::class ]]],    
     *          self::PUT => [ self::PREPROCESS => [[ self::class => 'disallowPublicAccess', self::class ]]],    
     *          self::DELETE => [ self::PREPROCESS => [[ self::class => 'disallowPublicAccess', self::class ]]],
     *          self::REST_REQUEST_FINNISH_CALLBACKS => [ self::PREPROCESS => [[ self::class => 'disallowPublicAccess', self::class ]]]    
     *      ];
     * @Note you can remove the constant entirely and re-run rest builder to reset the default.
     *
     * @Note: the following may be uncommented and used to allow explicitly referencing methods with callbacks. No 
     * parameters will be passed to the callbacks. The refrencing style above will also be respected in this array. The
     * example callables maybe removed. The static array value will be merged using php `[] + []` with the the public ( static += public ).
     *
     *    public function __construct(array &\$return = [])
     *    {
     *        parent::__construct(\$return);
     *        
     *        \$this->PHP_VALIDATION = [ 
     *            self::REST_REQUEST_PREPROCESS_CALLBACKS => [ 
     *                self::PREPROCESS => [
     *                    static fn() => self::disallowPublicAccess(self::class)
     *                ]
     *            ]
     *        ];
     *    }
     *
     *  @version ^11.3
     */{{^php_validation}}
    public const PHP_VALIDATION = [];{{/php_validation}} 
    {{#php_validation}} 
    {{{php_validation}}} 
    {{/php_validation}}
    
    {{^PHP_VALIDATION_PUBLIC}}
    public array \$PHP_VALIDATION = [
        
    ];
    {{/PHP_VALIDATION_PUBLIC}}
    {{#PHP_VALIDATION_PUBLIC}}
    {{PHP_VALIDATION_PUBLIC}}
    {{/PHP_VALIDATION_PUBLIC}}
   
    /**
     * CREATE_TABLE_SQL is autogenerated and should not be manually updated. Make changes in MySQL and regenerate using
     * the RestBuilder program.
     */
    public const CREATE_TABLE_SQL = /** @lang MySQL */ <<<MYSQL
{{createTableSQL}}
MYSQL;
       
   /**
    * Please reference these notes for the `get` method.
    * Nested aggregation is not currently supported. It is recommended to avoid using 'AS' where possible. Sub-selects are 
    * allowed and do support 'as' aggregation. Refer to the static subSelect method parameters in the parent `Rest` class.
    * All supported aggregation is listed in the example below. Note while the WHERE and JOIN members are syntactically 
    * similar, and are moreover compiled through the same method, our aggregation is not. Please refer to this example 
    * when building your queries. In many cases sub-selects can be replaces using simple joins, this is highly recommended.
    *
    *   \$argv = [
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
    *              ANOTHER_EXAMPLE_TABLE::subSelect(\$primary, \$argv, \$as, \$pdo, \$database)
    *       ],
    *       Rest::WHERE => [
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
    *                    Location_References::ENTITY_KEY => \$custom_var,
    *                   
    *                ],
    *                Example_Table_Two::CLASS_NAME => [
    *                    Example_Table_Two::ID => Example_Table_Two::subSelect(\$primary, \$argv, \$as, \$pdo, \$database)
    *                    ect... 
    *                ]
    *            ]
    *        ],
    *        Rest::PAGINATION => [
    *              Rest::PAGE => (int) 0, // used for pagination which equates to 
    *                  // ... LIMIT ' . ((\$argv[self::PAGINATION][self::PAGE] - 1) * \$argv[self::PAGINATION][self::LIMIT]) 
    *                  //       . ',' . \$argv[self::PAGINATION][self::LIMIT];
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
    * @param array \$return{{#primaryExists}}
    * @param {{#multiplePrimary}}array{{/multiplePrimary}}{{^multiplePrimary}}string{{/multiplePrimary}}|null \$primary{{/primaryExists}}
    * @param array \$argv
    * @generated
    * @return bool
    */
    public static function get(array|null &\$return, {{#primaryExists}}{{#multiplePrimary}}array{{/multiplePrimary}}{{^multiplePrimary}}string{{/multiplePrimary}}|null \$primary = null, {{/primaryExists}}array \$argv = []): bool
    {
        return self::select(\$return, \$argv{{#primaryExists}}, {{#multiplePrimary}}\$primary{{/multiplePrimary}}{{^multiplePrimary}}\$primary === null ? null : [ self::PRIMARY => \$primary ]{{/multiplePrimary}}{{/primaryExists}});
    }

    /**
     * @param array \$post - a one to one; column => value mapping. Multiple rows may be inserted at one time using an array of arrays.
     * @return bool|string{{#primaryExists}}|mixed{{/primaryExists}}
     * @generated
     */
    public static function post(array &\$post = []){{^primaryExists}}: bool{{/primaryExists}}
    {   
        return self::insert(\$post);
    }
    
    /**
    * 
    * {{^primaryExists}}
    *  Syntax should be as follows.
    *  \$argv = [
    *       Rest::UPDATE => [
    *              ...
    *       ],
    *       Rest::WHERE => [
    *              ...
    *       ]
    * {{/primaryExists}}{{#primaryExists}}
    * Tables where primary keys exist must be updated by its primary key. 
    * Column should be in a key value pair passed to \$argv or optionally using syntax:
    * \$argv = [
    *       Rest::UPDATE => [
    *              ...
    *       ]
    * ]
    * {{/primaryExists}}
    * @param array \$returnUpdated - will be merged with with array_merge, with a successful update. 
    {{#primaryExists}}* @param {{#multiplePrimary}}array{{/multiplePrimary}}{{^multiplePrimary}}string{{/multiplePrimary}}|null \$primary{{/primaryExists}}
    * @param array \$argv 
    * @generated
    * @return bool - if execute fails, false will be returned and \$returnUpdated = \$stmt->errorInfo(); 
    */
    public static function put(array &\$returnUpdated, {{#primaryExists}}{{#multiplePrimary}}array{{/multiplePrimary}}{{^multiplePrimary}}string{{/multiplePrimary}}|null \$primary = null,{{/primaryExists}} array \$argv = []) : bool
    {
        return self::updateReplace(\$returnUpdated, \$argv{{#primaryExists}}, {{#multiplePrimary}}\$primary{{/multiplePrimary}}{{^multiplePrimary}}\$primary === null ? null : [ self::PRIMARY => \$primary ]{{/multiplePrimary}}{{/primaryExists}});
    }

    /**
    * @param array \$remove{{#primaryExists}}
    * @param {{#multiplePrimary}}array{{/multiplePrimary}}{{^multiplePrimary}}string{{/multiplePrimary}}|null \$primary{{/primaryExists}}
    * @param array \$argv
    * @generated
    * @return bool
    */
    public static function delete(array &\$remove, {{#primaryExists}}{{#multiplePrimary}}array{{/multiplePrimary}}{{^multiplePrimary}}string{{/multiplePrimary}}|null \$primary = null, {{/primaryExists}}array \$argv = []) : bool
    {
        return self::remove(\$remove, \$argv{{#primaryExists}}, {{#multiplePrimary}}\$primary{{/multiplePrimary}}{{^multiplePrimary}}\$primary === null ? null : [ self::PRIMARY => \$primary ]{{/multiplePrimary}}{{/primaryExists}});
    }
    
}

STRING;

    }

}