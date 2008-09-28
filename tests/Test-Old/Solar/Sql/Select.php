<?php
/**
 * 
 * Concrete class test.
 * 
 */
class Test_Solar_Sql_Select extends Solar_Test {
    
    /**
     * 
     * Configuration values.
     * 
     * @var array
     * 
     */
    protected $_Test_Solar_Sql_Select = array(
    );
    
    // -----------------------------------------------------------------
    // 
    // Support methods.
    // 
    // -----------------------------------------------------------------
    
    /**
     * 
     * Constructor.
     * 
     * @param array $config User-defined configuration parameters.
     * 
     */
    public function __construct($config)
    {
        parent::__construct($config);
    }
    
    /**
     * 
     * Destructor; runs after all methods are complete.
     * 
     * @param array $config User-defined configuration parameters.
     * 
     */
    public function __destruct()
    {
        parent::__destruct();
    }
    
    /**
     * 
     * Setup; runs before each test method.
     * 
     */
    public function setup()
    {
        parent::setup();
    }
    
    /**
     * 
     * Setup; runs after each test method.
     * 
     */
    public function teardown()
    {
        parent::teardown();
    }
    
    // -----------------------------------------------------------------
    // 
    // Test methods.
    // 
    // -----------------------------------------------------------------
    
    /**
     * 
     * Test -- Constructor.
     * 
     */
    public function test__construct()
    {
        $obj = Solar::factory('Solar_Sql_Select');
        $this->assertInstance($obj, 'Solar_Sql_Select');
    }
    
    /**
     * 
     * Test -- Returns this object as an SQL statement string.
     * 
     */
    public function test__toString()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds data to bind into the query.
     * 
     */
    public function testBind()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Clears query properties and record sources.
     * 
     */
    public function testClear()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Get the count of rows and number of pages for the current query.
     * 
     */
    public function testCountPages()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Makes the query SELECT DISTINCT.
     * 
     */
    public function testDistinct()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Fetch the results based on the current query properties.
     * 
     */
    public function testFetch()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- 
     * 
     */
    public function testFetchAll()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- 
     * 
     */
    public function testFetchAssoc()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- 
     * 
     */
    public function testFetchCol()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- 
     * 
     */
    public function testFetchOne()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- 
     * 
     */
    public function testFetchPairs()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- 
     * 
     */
    public function testFetchPdo()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- 
     * 
     */
    public function testFetchRow()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- 
     * 
     */
    public function testFetchRowset()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- 
     * 
     */
    public function testFetchSql()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- 
     * 
     */
    public function testFetchValue()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds a FROM table and columns to the query.
     * 
     */
    public function testFrom()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds a sub-select and columns to the query.
     * 
     */
    public function testFromSelect()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Gets the number of rows per page.
     * 
     */
    public function testGetPaging()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds grouping to the query.
     * 
     */
    public function testGroup()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds a HAVING condition to the query by AND.
     * 
     */
    public function testHaving()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds an INNER JOIN table and columns to the query.
     * 
     */
    public function testInnerJoin()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds a JOIN table and columns to the query.
     * 
     */
    public function testJoin()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds a LEFT JOIN table and columns to the query.
     * 
     */
    public function testLeftJoin()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Sets a limit count and offset to the query.
     * 
     */
    public function testLimit()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Sets the limit and count by page number.
     * 
     */
    public function testLimitPage()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds multiple HAVING conditions to the query.
     * 
     */
    public function testMultiHaving()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds multiple WHERE conditions to the query.
     * 
     */
    public function testMultiWhere()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds a HAVING condition to the query by OR.
     * 
     */
    public function testOrHaving()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds a WHERE condition to the query by OR.
     * 
     */
    public function testOrWhere()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds a row order to the query.
     * 
     */
    public function testOrder()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Safely quotes a value for an SQL statement.
     * 
     */
    public function testQuote()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Quotes a value and places into a piece of text at a placeholder.
     * 
     */
    public function testQuoteInto()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Quote multiple text-and-value pieces.
     * 
     */
    public function testQuoteMulti()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Sets the number of rows per page.
     * 
     */
    public function testSetPaging()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Unsets bound data.
     * 
     */
    public function testUnbind()
    {
        $this->todo('stub');
    }
    
    /**
     * 
     * Test -- Adds a WHERE condition to the query by AND.
     * 
     */
    public function testWhere()
    {
        $this->todo('stub');
    }


}