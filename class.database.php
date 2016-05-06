<?php

class Database
{
    private $sHost		= DB_HOST;
    private $sUser		= DB_USER;
    private $sPassword	= DB_PASSWORD;
    private $sDBName	= DB_NAME;

    private static $dbh;
    private $error;

    private $stmt;

    public function __construct()
    {
        // Set DSN
        $sDSN = 'mysql:host=' . $this->sHost . ';dbname=' . $this->sDBName;
        // Set options
        $options = array(
            PDO::ATTR_PERSISTENT    => false,
            PDO::ATTR_ERRMODE       => PDO::ERRMODE_EXCEPTION
        );
        // Create a new PDO instanace
        try
        {
            if(!self::$dbh)
            	self::$dbh = new PDO($sDSN, $this->sUser, $this->sPassword, $options);
        }
        // Catch any errors
        catch(PDOException $e)
        {
            throw new exception( $e->getMessage() );
        }
    }

    /**
     * Prepare a SQL for a query
     * @param string $sSQL
     */
    public function query($sSQL)
    {
    	$this->stmt = self::$dbh->prepare($sSQL);
    }


    public function bindArray($array)
    {
		foreach($array as $key=>$argument)
		{
			$this->bind($key, $argument);
		}
    }

    /**
     * Bind a value to SQL query. Can manually set value type.
     * @param string $param
     * @param unknown $value
     * @param string $type
     */
    public function bind($param, $value, $type = null)
    {
    	if (is_null($type))
    	{
    		switch (true)
    		{
    			case is_int($value):
    				$type = PDO::PARAM_INT;
    				break;
    			case is_bool($value):
    				$type = PDO::PARAM_BOOL;
    				break;
    			case is_null($value):
    				$type = PDO::PARAM_NULL;
    				break;
    			default:
    				$type = PDO::PARAM_STR;
    		}
    	}
    	$this->stmt->bindValue($param, $value, $type);
    }

    /**
     *  Run the query with bound values.
     */
    public function execute()
    {
    	global $objSession;

    	try
    	{
    		$bReturn = $this->stmt->execute();
			return $bReturn;
    	}
    	catch(PDOException $e)
    	{
    		if($this->isInTransaction())
    			$this->cancelTransaction();
    		$aTrace = $e->getTrace();
    		//logError('FATAL', $e->getMessage(), $aTrace[1]['file'], '', $aTrace[1]['line'], $objSession->getSessionID(), $objSession->getUserID(), 'MySQL');
    		throw $e;
    	}
    }

    /**
     * Return array of all results
     */
    public function resultset()
    {
    	$this->execute();
    	return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /*
     * Return array of all values.
     */
    public function single()
    {
    	$this->execute();
    	return $this->stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Return number of rows returned count.
     */
    public function rowCount()
    {
    	return $this->stmt->rowCount();
    }

    /**
     * Return ID of last inserted row
     */
    public function lastInsertId()
    {
    	return self::$dbh->lastInsertId();
    }

    public function beginTransaction()
    {
    	return self::$dbh->beginTransaction();
    }

    public function endTransaction()
    {
    	return self::$dbh->commit();
    }

    public function isInTransaction()
    {
    	return self::$dbh->inTransaction();
    }

    public function cancelTransaction()
    {
    	return self::$dbh->rollBack();
    }

    public function debugDumpParams()
    {
    	return $this->stmt->debugDumpParams();
    }
}
