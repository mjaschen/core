<?php
// <http://ar.rubyonrails.com/classes/ActiveRecord/Associations/ClassMethods.html>
abstract class Solar_Sql_Model_Related extends Solar_Base {
    
    public $name;
    
    public $type;
    
    /**
     * 
     * The class of the native model.
     * 
     * @var string
     * 
     */
    public $native_class;
    
    /**
     * 
     * The name of the native table.
     * 
     * @var string
     * 
     */
    public $native_table;
    
    /**
     * 
     * The alias for the native table.
     * 
     * @var string
     * 
     */
    public $native_alias;
    
    /**
     * 
     * The native column to match against the foreign primary column.
     * 
     * @var string
     * 
     */
    public $native_col;
    
    /**
     * 
     * `foreign_class`
     * : (string) The class name of the foreign model. Default is the first
     *   matching class for the relationship name, as loaded from the parent
     *   class stack. Automatically honors single-table inheritance.
     * 
     * 
     * @var string
     * 
     */
    public $foreign_class;
    
    /**
     * 
     * `foreign_table`
     * : (string) The name of the table for the foreign model. Default is the
     *   table specified by the foreign model.
     * 
     * @var string
     * 
     */
    public $foreign_table;
    
    /**
     * 
     * `foreign_alias`
     * : (string) Aliases the foreign table to this name. Default is the
     *   relationship name.
     * 
     * @var string
     * 
     */
    public $foreign_alias;
    
    /**
     * 
     * `foreign_col`
     * : (string) The name of the column to join with in the *foreign* table.
     *   This forms one-half of the relationship.  Default is per association
     *   type.
     * 
     * @var string
     * 
     */
    public $foreign_col;
    
    /**
     * 
     * The name of the foreign primary column.
     * 
     * @var string
     * 
     */
    public $foreign_primary_col;
    
    /**
     * 
     * `foreign_inherit_col`
     * : (string) If the foreign model uses single-table inheritance, this is
     *   the column where the inheritance value is stored.
     * 
     * 
     * @var string
     * 
     */
    public $foreign_inherit_col;
    
    /**
     * 
     * `foreign_inherit_val`
     * : (string) If the foreign model has an inheritance type, the value of
     *   that inheritance type (as stored in foreign_inherit_col).
     * 
     * @var string
     * 
     */
    public $foreign_inherit_val;
    
    /**
     * 
     * The relationship name through which we find foreign records.
     * 
     * @var string
     * 
     */
    public $through;
    
    /**
     * 
     * The "through" table name.
     * 
     * @var string
     * 
     */
    public $through_table;
    
    /**
     * 
     * The "through" table alias.
     * 
     * @var string
     * 
     */
    public $through_alias;
    
    /**
     * 
     * In the "through" table, the column that has the matching native value.
     * 
     * @var string
     * 
     */
    public $through_native_col;
    
    /**
     * 
     * In the "through" table, the column that has the matching foreign value.
     * 
     * @var string
     * 
     */
    public $through_foreign_col;
    
    /**
     * 
     * When fetching records, use DISTINCT ?
     * 
     * @var bool
     * 
     */
    public $distinct;
    
    /**
     * 
     * Fetch these columns for the related records.
     * 
     * @var string|array
     * 
     */
    public $cols;
    
    /**
     * 
     * Additional WHERE clauses when fetching records.
     * 
     * @var string|array
     * 
     */
    public $where;
    
    /**
     * 
     * Additional GROUP clauses when fetching records.
     * 
     * @var string|array
     * 
     */
    public $group;
    
    /**
     * 
     * Additional HAVING clauses when fetching records.
     * 
     * @var string|array
     * 
     */
    public $having;
    
    /**
     * 
     * Additional ORDER clauses when fetching records.
     * 
     * @var string|array
     * 
     */
    public $order;
    
    /**
     * 
     * When fetching records, use this many records per page of results.
     * 
     * @var int
     * 
     */
    public $paging;
    
    /**
     * 
     * The fetch type to use: 'one', 'all', 'assoc', etc.
     * 
     * @var string
     * 
     */
    public $fetch;
    
    /**
     * 
     * There is a virtual element called `foreign_key` that automatically
     * populates the `native_col` or `foreign_col` value for you, based on the
     * association type.  This will be used **only** when `native_col` **and**
     * `foreign_col` are not set.
     * 
     * @var string
     * 
     */
    // public $foreign_key;
    
    /**
     * 
     * There is a virtual element called `through_key` that automatically 
     * populates the 'through_foreign_col' value for you.
     * 
     * @var string.
     * 
     */
    // public $through_key;
    
    protected $_native_model;
    
    protected $_foreign_model;
    
    public function setNativeModel($model)
    {
        $this->_native_model = $model;
        $this->native_class = $this->_native_model->class;
        $this->native_table = $this->_native_model->table_name;
        $this->native_alias = $this->_native_model->model_name;
    }
    
    // gets the *related* model, not the native model
    public function getModel()
    {
        return $this->_foreign_model;
    }
    
    public function toArray()
    {
        $vars = get_object_vars($this);
        foreach ($vars as $key => $val) {
            if ($key[0] == '_') {
                unset($vars[$key]);
            }
        }
        return $vars;
    }
    
    /**
     * 
     * Corrects the relationship definitions.
     * 
     * @return void
     * 
     */
    public function load($opts)
    {
        $this->name = $opts['name'];
        $this->_setType();
        $this->_setForeignModel($opts);
        $this->_setCols($opts);
        $this->_setSelect($opts);
        
        // if the user has specified *neither* a foreign_col *nor* a native_col,
        // but *has* specified a foreign_key, use the foreign_key to define 
        // the foreign_col or native col (depending on relation type). 
        if (empty($opts['native_col']) &&
            empty($opts['foreign_col']) &&
            ! empty($opts['foreign_key'])) {
            // redefine based on the "virtual" foreign_key value
            $this->_fixRelatedCol($opts);
        }
        
        $this->_setRelated($opts);
    }
    
    public function newSelect($spec)
    {
        // specification must be a record, or params for a select
        if (! ($spec instanceof Solar_Sql_Model_Record) && ! is_array($spec)) {
            throw $this->_exception('ERR_RELATED_SPEC', array(
                'spec' => $spec
            ));
        }
        
        // convert $spec array to a Select object for the native column ID list
        if (is_array($spec)) {
            // build the select
            $params = $spec;
            $spec = $this->_native_model->newSelect();
            $spec->distinct($params['distinct'])
                 ->from("{$this->native_table} AS {$this->native_alias}", $this->native_col)
                 ->multiWhere($params['where'])
                 ->group($params['group'])
                 ->having($params['having'])
                 ->order($params['order'])
                 ->setPaging($params['paging'])
                 ->limitPage($params['page']);
        }
        
        // get a select object for the related rows
        $select = Solar::factory(
            $this->_native_model->select_class,
            array('sql' => $this->_native_model->sql)
        );
        
        // modify the select per-relationship. only has-many-through uses
        // non-standard modification.
        $this->_modSelect($select, $spec);
        
        // add remaining clauses
        $select->distinct($this->distinct)
               ->multiWhere($this->where)
               ->group($this->group)
               ->having($this->having)
               ->order($this->order)
               ->setPaging($this->paging);
        
        // done
        return $select;
    }
    
    protected function _modSelect($select, $spec)
    {
        // simple belongs_to, has_one, or has_many.
        if ($spec instanceof Solar_Sql_Model_Record) {
            // restrict to the related native column value in the foreign table
            $select->where(
                "{$this->foreign_alias}.{$this->foreign_col} = ?",
                $spec->{$this->native_col}
            );
        } else {
            // $spec is a Select object
            // restrict to a sub-select of IDs from the native table
            $inner = str_replace("\n", "\n\t\t", $spec->fetchSql());
            // add the native table ID at the top through a join
            $select->innerJoin(
                "($inner) AS {$this->native_alias}",
                "{$this->foreign_alias}.{$this->foreign_col} = {$this->native_alias}.{$this->native_col}",
                "{$this->native_col} AS {$this->native_alias}__{$this->native_col}"
            );
        }
        
        // select columns from the foreign table.
        $select->from(
            "{$this->foreign_table} AS {$this->foreign_alias}",
            $this->cols
        );
        
        // honor foreign inheritance
        if ($this->foreign_inherit_col) {
            $select->where(
                "{$this->foreign_alias}.{$this->foreign_inherit_col} = ?",
                $this->foreign_inherit_val
            );
        }
    }
    
    protected function _setForeignModel($opts)
    {
        // make sure we have at least a base class name
        if (empty($opts['foreign_class'])) {
            $this->foreign_class = $opts['name'];
        } else {
            $this->foreign_class = $opts['foreign_class'];
        }
        
        // can we load a related model class from the hierarchy stack?
        $class = $this->_native_model->stack->load($this->foreign_class, false);
        
        // did we find it?
        if (! $class) {
            // look for a "parallel" class name, based on where the word
            // "Model" is in the current class name. this lets you pull
            // model classes from the same level, not from the inheritance
            // stack.
            $pos = strrpos($this->native_class, 'Model_');
            if ($pos !== false) {
                $pos += 6; // "Model_"
                $tmp = substr($this->native_class, 0, $pos) . ucfirst($this->foreign_class);
                try {
                    Solar::loadClass($tmp);
                    // if no exception, $class gets set
                    $class = $tmp;
                } catch (Exception $e) {
                    // do nothing
                }
            }
        }
        
        // last chance: do we *still* need a class name?
        if (! $class) {
            // not in the hierarchy, and no parallel class name. look for the
            // model class literally. this will throw an exception if the
            // class cannot be found anywhere.
            try {
                Solar::loadClass($this->foreign_class);
                // if no exception, $class gets set
                $class = $this->foreign_class;
            } catch (Solar_Exception $e) {
                throw $this->_exception('ERR_LOAD_FOREIGN_MODEL', array(
                    'native_model' => $this->_native_model->class,
                    'related_name' => $opts['name'],
                    'foreign_class' => $this->foreign_class,
                ));
            }
        }
        
        // finally we have a class name, keep it as the foreign model class
        $this->foreign_class = $class;
        
        // create a foreign model instance
        $this->_foreign_model = Solar::factory( $this->foreign_class, array(
            'sql' => $this->_native_model->sql
        ));
        
        // get its table name
        $this->foreign_table = $this->_foreign_model->table_name;
        
        // and its primary column
        $this->foreign_primary_col = $this->_foreign_model->primary_col;
        
        // set the foreign alias based on the relationship name
        $this->foreign_alias = $opts['name'];
    }
    
    
    protected function _setCols($opts)
    {
        // the list of foreign table cols to retrieve
        if (empty($opts['cols'])) {
            $this->cols = $this->_foreign_model->fetch_cols;
        } elseif (is_string($opts['cols'])) {
            $this->cols = explode(',', $opts['cols']);
        } else {
            $this->cols = (array) $opts['cols'];
        }
        
        // make sure we always retrieve the foreign primary key value,
        // if there is one.
        $primary = $this->_foreign_model->primary_col;
        if ($primary && ! in_array($primary, $this->cols)) {
            $this->cols[] = $primary;
        }
        
        // if inheritance is turned on for the foreign model,
        // make sure we always retrieve the foreign inheritance value.
        $inherit = $this->_foreign_model->inherit_col;
        if ($inherit && ! in_array($inherit, $this->cols)) {
            $this->cols[] = $inherit;
        }
        
        // if inheritance is turned on, force the foreign_inherit
        // column and value
        if ($this->_foreign_model->inherit_col && $this->_foreign_model->inherit_model) {
            $this->foreign_inherit_col = $this->_foreign_model->inherit_col;
            $this->foreign_inherit_val = $this->_foreign_model->inherit_model;
        } else {
            $this->foreign_inherit_col = null;
            $this->foreign_inherit_val = null;
        }
    }
    
    protected function _setSelect($opts)
    {
        // distinct
        if (empty($opts['distinct'])) {
            $this->distinct = false;
        } else {
            $this->distinct = (bool) $opts['distinct'];
        }
        
        // where
        if (empty($opts['where'])) {
            $this->where = null;
        } else {
            $this->where = (array) $opts['where'];
        }
        
        // group
        if (empty($opts['group'])) {
            $this->group = null;
        } else {
            $this->group = (array) $opts['group'];
        }
        
        // having
        if (empty($opts['having'])) {
            $this->having = null;
        } else {
            $this->having = (array) $opts['having'];
        }
        
        // order
        if (empty($opts['order'])) {
            // default to the foreign primary key
            $this->order = array("{$this->foreign_alias}.{$this->_foreign_model->primary_col}");
        } else {
            $this->order = (array) $opts['order'];
        }
        
        // paging from the foreign model
        if (empty($opts['paging'])) {
            $this->paging = $this->_foreign_model->paging;
        } else {
            $this->paging = (int) $opts['paging'];
        }
    }
    
    abstract protected function _setType();
    
    abstract protected function _fixRelatedCol(&$opts);
    
    abstract protected function _setRelated($opts);
}