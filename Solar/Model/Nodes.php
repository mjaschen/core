<?php
/**
 * 
 * A model of content "nodes" (individual pieces of content).
 * 
 * @category Solar
 * 
 * @package Solar_Model
 * 
 * @author Paul M. Jones <pmjones@solarphp.com>
 * 
 * @license http://opensource.org/licenses/bsd-license.php BSD
 * 
 * @version $Id$
 * 
 */
class Solar_Model_Nodes extends Solar_Model
{
    /**
     * 
     * Model setup.
     * 
     * @return void
     * 
     */
    protected function _setup()
    {
        /**
         * Table name, columns, and indexes.
         */
        
        $this->_table_name = 'nodes';
        
        $this->_table_cols = array(
            'id' => array(
                'type'    => 'int',
                'require' => true,
                'primary' => true,
                'autoinc' => true,
            ),
            'created' => 'timestamp',
            'updated' => 'timestamp',
            'area_id' => array(
                'type'    => 'int',
                'require' => true,
            ),
            'inherit' => array(
                'type'    => 'varchar',
                'size'    => 32,
            ),
            'name' => array(
                'type'    => 'varchar',
                'size'    => 127,
            ),
            'parent_id' => 'int',
            'owner_handle' => array(
                'type'    => 'varchar',
                'size'    => 255,
            ),
            'editor_handle' => array(
                'type'    => 'varchar',
                'size'    => 255,
            ),
            'editor_ipaddr' => array(
                'type'    => 'varchar',
                'size'    => 15,
            ),
            'assign_handle' => array(
                'type'    => 'varchar',
                'size'    => 255,
            ),
            'locale' => array(
                'type'    => 'varchar',
                'size'    => 5,
            ),
            'rating' => 'int',
            'publish' => 'timestamp',
            'email' => array(
                'type'    => 'varchar',
                'size'    => 255,
            ),
            'uri' => array(
                'type'    => 'varchar',
                'size'    => 255,
            ),
            'moniker' => array(
                'type'    => 'varchar',
                'size'    => 255,
            ),
            'pos' => 'int',
            'status' => array(
                'type'    => 'varchar',
                'size'    => 32,
            ),
            'mime' => array(
                'type'    => 'varchar',
                'size'    => 64,
                'default' => 'text/plain',
            ),
            'subj' => array(
                'type'    => 'varchar',
                'size'    => 255,
            ),
            'summ' => 'clob',
            'body' => 'clob',
            'prefs' => 'clob',
        );
        
        $this->_index = array(
            'created',
            'updated',
            'area_id',
            'name',
            'parent_id',
            'owner_handle',
            'assign_handle',
            'inherit',
            'locale',
            'pos',
            'rating',
            'publish',
            'uri',
            'email',
            'status',
        );
        
        /**
         * Special columns
         */
        $this->_serialize_cols[] = 'prefs';
        $this->_calculate_cols[] = 'tags_as_string';
        
        /**
         * Filters
         */
        
        // make sure the name is unique for its area and model
        $where = array(
            'inherit = :inherit',
            'area_id = :area_id',
        );
        $this->_addFilter('name', 'validateUnique', $where);
        
        // other filters
        $this->_addFilter('email', 'validateEmail');
        $this->_addFilter('uri', 'validateUri');
        $this->_addFilter('editor_ipaddr', 'validateIpv4');
        $this->_addFilter('locale', 'validateLocaleCode');
        $this->_addFilter('mime', 'validateMimeType');
        $this->_addFilter('tags_as_string', 'validateSepWords');
        
        /**
         * Relationships.
         */
        $this->_belongsTo('area');
        
        $this->_hasMany('taggings');
        
        $this->_hasMany('tags', array(
            'through' => 'taggings',
        ));
    }
    
    /**
     * 
     * Fetches a collection of nodes with certain tags.
     * 
     * @param array $tag_list Fetch only nodes with all of these tags. If
     * empty, will fetch all nodes.
     * 
     * @param array $params Added parameters for the SELECT.
     * 
     * @return Solar_Model_Nodes_Collection
     * 
     */
    public function fetchAllByTags($tag_list, $params = null)
    {
        $tag_list = $this->_fixTagList($tag_list);
        if ($tag_list) {
            
            // eager-join to tags
            $params['eager']['tags'] = array(
                'where' => array(
                    'tags.name IN (?)' => $tag_list,
                ),
            );
            
            // group by the model primary key so that multiple tag matches
            // only return one row
            $native_primary = $this->getPrimary();
            $params['group'][] = $native_primary;
            
            // make sure that all tags match
            $params['having']["COUNT($native_primary) = ?"] = count($tag_list);
        }
        
        return $this->fetchAll($params);
    }
    
    /**
     * 
     * Support method to "fix" tag-list arrays: no duplicates, no spaces, etc.
     * 
     * @param array $tag_list The list of tags to "fix".
     * 
     * @return array The fixed tag list.
     * 
     */
    protected function _fixTagList($tag_list)
    {
        // convert to array
        if (! is_array($tag_list)) {
            $tag_list = preg_split('/\s+/', trim((string) $tag_list));
        }
        
        // no duplicates allowed
        $tag_list = array_unique($tag_list);
        
        // if the string tag-list was empty, the preg-split leaves one empty
        // element in the array.
        if (count($tag_list) == 1 && reset($tag_list) == '') {
            $tag_list = array();
        }
        
        // done!
        return $tag_list;
    }
}
