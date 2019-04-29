<?php


namespace Owuan\Mysql;

use Exception;
use PDO as BasePDO;
use Swoole\Coroutine\Mysql;

/**
 * Class PDO
 */
class PDO extends BasePDO
{

    /**
     * @var array
     */
    private static $keyMap = [
        'dbname' => 'database',
    ];

    /**
     * @var array
     */
    private static $options = [
        'host' => '',
        'port' => 3306,
        'user' => '',
        'password' => '',
        'database' => '',
        'charset' => 'utf8mb4',
        'strict_type' => true,
        'timeout' => -1,
    ];

    /** @var \Swoole\Coroutine\Mysql */
    protected $client;

    /**
     * @var bool
     */
    private $inTransaction = false;

    /**
     * PDO constructor.
     *
     * @param string $dsn
     * @param string $username
     * @param string $password
     * @param array $options
     *
     * @throws ConnectionException
     */
    public function __construct(string $dsn, string $username = null, string $password = null, array $options = [])
    {

        try {
            parent::__construct($dsn, $username, $password, $options);
            $this->setClient();


            $this->connect($this->getOptions(
                $dsn,
                $username,
                $password,
                $options
            ));
        } catch (\PDOException $exception) {

            throw new ConnectionException($exception->getMessage());
        }

    }

    /**
     * @param mixed $client
     */
    public function setClient($client = null)
    {
        $this->client = $client ?: new Mysql();
    }

    /**
     * @return Mysql
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param array $options
     *
     * @return $this
     * @throws \SwooleTW\Http\Coroutine\ConnectionException
     */
    protected function connect(array $options = [])
    {
        $this->client->connect($options);


        if (!$this->client->connected) {
            $message = $this->client->connect_error ?: $this->client->error;
            $errorCode = $this->client->connect_errno ?: $this->client->errno;

            throw new ConnectionException($message, $errorCode);
        }

        return $this;
    }

    /**
     * @param $dsn
     * @param $username
     * @param $password
     * @param $driverOptions
     *
     * @return array
     */
    protected function getOptions($dsn, $username, $password, $driverOptions)
    {
        $dsn = explode(':', $dsn);
        $driver = ucwords(array_shift($dsn));
        $dsn = explode(';', implode(':', $dsn));
        $configuredOptions = [];

        static::checkDriver($driver);

        foreach ($dsn as $kv) {
            $kv = explode('=', $kv);
            if (count($kv)) {
                $configuredOptions[$kv[0]] = $kv[1] ?? '';
            }
        }

        $authorization = [
            'user' => $username,
            'password' => $password,
        ];

        $configuredOptions = $driverOptions + $authorization + $configuredOptions;

        foreach (static::$keyMap as $pdoKey => $swpdoKey) {
            if (isset($configuredOptions[$pdoKey])) {
                $configuredOptions[$swpdoKey] = $configuredOptions[$pdoKey];
                unset($configuredOptions[$pdoKey]);
            }
        }

        return array_merge(self::$options, $configuredOptions);
    }

    /**
     * @param string $driver
     */
    public static function checkDriver(string $driver)
    {
        if (!in_array($driver, static::getAvailableDrivers(), true)) {
            throw new \InvalidArgumentException("{$driver} driver is not supported yet.");
        }
    }

    /**
     *  swoole only supports mysql
     *
     * @return array
     */
    public static function getAvailableDrivers()
    {
        return ['Mysql'];
    }

    /**
     * @return bool
     */
    public function beginTransaction()
    {
        $this->inTransaction = true;

        return $this->client->begin();
    }

    /**
     * @return bool
     */
    public function rollBack()
    {
        $rollback = $this->client->rollback();
        $this->inTransaction = false;

        return $rollback;
    }

    /**
     * @return bool|void
     */
    public function commit()
    {
        $this->inTransaction = true;

        return $this->client->commit();;
    }

    /**
     * @return bool
     */
    public function inTransaction()
    {
        return $this->inTransaction;
    }

    /**
     * @param string|null $seqname
     *
     * @return int|string
     */
    public function lastInsertId($seqname = null)
    {
        return $this->client->insert_id;
    }

    /**
     * @return mixed|void
     */
    public function errorCode()
    {
        return $this->client->errno;
    }

    /**
     * @return array
     */
    public function errorInfo()
    {
        return [
            $this->client->errno,
            $this->client->errno,
            $this->client->error,
        ];
    }

    /**
     * @param string $statement
     *
     * @return int
     */
    public function exec($statement): int
    {
        $this->query($statement);

        return $this->client->affected_rows;
    }

    /**
     * @param string $statement
     * @param int $mode
     * @param mixed $arg3
     * @param array $ctorargs
     *
     * @return array|bool|false|\PDOStatement
     * @throws QueryException
     */
    public function query($statement, $mode = PDO::ATTR_DEFAULT_FETCH_MODE, $arg3 = null, array $ctorargs = [])
    {
        $result = $this->client->query($statement, self::$options['timeout'] ?? null);

        if ($result === false) {
            throw new QueryException($this->client->error, $this->client->errno);
        }

        return $result;
    }

    /**
     * @param string $statement
     * @param array $options
     *
     * @return bool|\PDOStatement|\SwooleTW\Http\Coroutine\PDOStatement
     * @throws QueryException
     */
    public function prepare($statement, $options = null)
    {
        $options = $options ?? [];

        if (strpos($statement, ':') !== false) {
            $i = 0;
            $bindKeyMap = [];
            $statement = preg_replace_callback('/:([a-zA-Z_]\w*?)\b/', static function ($matches) use (&$i, &$bindKeyMap) {
                $bindKeyMap[$matches[1]] = $i++;

                return '?';
            }, $statement);
        }

        $stmtObj = $this->client->prepare($statement);

        if ($stmtObj) {
            $stmtObj->bindKeyMap = $bindKeyMap ?? [];

            return new PDOStatement($this, $stmtObj, $options);
        }


        throw new QueryException($statement . " : " . $this->client->error, $this->client->errno);
    }


    /**
     * @param int $attribute
     *
     * @return bool|mixed|string
     */
    public function getAttribute($attribute)
    {
        switch ($attribute) {
            case self::ATTR_AUTOCOMMIT:
                return true;
            case self::ATTR_CASE:
            case self::ATTR_CLIENT_VERSION:
            case self::ATTR_CONNECTION_STATUS:
                return $this->client->connected;
            case self::ATTR_DRIVER_NAME:
            case self::ATTR_ERRMODE:
                return 'Swoole Style';
            case self::ATTR_ORACLE_NULLS:
            case self::ATTR_PERSISTENT:
            case self::ATTR_PREFETCH:
            case self::ATTR_SERVER_INFO:
            case self::ATTR_SERVER_VERSION:
                return 'Swoole Mysql';
            case self::ATTR_TIMEOUT:
                return self::$options['timeout'];
            default:
                throw new \InvalidArgumentException('Not implemented yet!');
        }
    }

    /**
     * @param string $string
     * @param null $paramtype
     *
     * @return string|void
     */
    public function quote($string, $paramtype = null)
    {
        $search = array("\\", "\x00", "\n", "\r", "'", '"', "\x1a");
        $replace = array("\\\\", "\\0", "\\n", "\\r", "\'", '\"', "\\Z");

        return str_replace($search, $replace, $string);
    }

    /**
     * Destructor
     */
    public function __destruct()
    {
        $this->client->close();
    }
}
