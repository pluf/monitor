<?php

/**
 * Monitor
 *
 * @author maso <mostafa.barmshory@dpq.co.ir>
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *
 */
class Monitor_Property extends Pluf_Model
{

    /**
     *
     * {@inheritdoc}
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'monitor_property';
        $this->_a['cols'] = array(
            'id' => array(
                'type' => 'Pluf_DB_Field_Sequence',
                'blank' => true,
                'editable' => false,
                'readable' => true
            ),
            'name' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => false,
                'size' => 100,
                'verbose' => __('property name'),
                'help_text' => __('The property name must be unique for each application.'),
                'editable' => false,
                'readable' => true
            ),
            'title' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'size' => 50,
                'editable' => true,
                'readable' => true
            ),
            'description' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'size' => 250,
                'editable' => true,
                'readable' => true
            ),
            'function' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'size' => 100,
                'editable' => false,
                'readable' => false
            ),
            'creation_dtime' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'blank' => true,
                'editable' => false,
                'readable' => true
            ),
            'modif_dtime' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'blank' => true,
                'editable' => false,
                'readable' => true
            ),
            // Relations
            'monitor' => array(
                'type' => 'Pluf_DB_Field_Foreignkey',
                'model' => 'Monitor',
                'blank' => false,
                'is_null' => false,
                'relate_name' => 'monitor',
                'editable' => false,
                'readable' => true
            )
        );
        
        $this->_a['idx'] = array(
            'monitor_idx' => array(
                'col' => 'monitor, name',
                'type' => 'unique', // normal, unique, fulltext, spatial
                'index_type' => '', // hash, btree
                'index_option' => '',
                'algorithm_option' => '',
                'lock_option' => ''
            )
        );
        
        $this->_a['views'] = array(
            'all' => array(
                'select' => $this->getSelect()
            )
            // 'beans' => array(
            // 'select' => 'bean AS bean_id, title, description, level',
            // 'group' => 'bean',
            // 'props' => array(
            // 'bean_id' => 'id'
            // )
            // ),
            // 'properties' => array(
            // 'select' => 'property AS property_id, title, description, level',
            // 'props' => array(
            // 'property_id' => 'id'
            // )
            // )
        );
    }

    /**
     * Call monitor property and get value
     *
     * @param Pluf_HTTP_Request $params
     * @return object
     */
    function invoke($request, $match = array())
    {
        $match['property'] = $this->name;
        return call_user_func_array(explode('::', $this->function), array(
            $request,
            $match
        ));
    }

    /**
     *
     * {@inheritdoc}
     * @see Pluf_Model::preSave()
     */
    function preSave($create = false)
    {
        if ($this->id == '') {
            $this->creation_dtime = gmdate('Y-m-d H:i:s');
        }
        $this->modif_dtime = gmdate('Y-m-d H:i:s');
    }

    /**
     * This function is used to load data in installation process.
     * Data must
     * contains monitor name.
     *
     * @param array $data
     */
    function initFromFormData($data)
    {
        $this->setFromFormData($data);
        $monitor = new Monitor();
        $monitor = $monitor->getOne('name=' . $data['monitor']);
        if(!isset($monitor) || $monitor->isAnonymous()){
            $monitor->name =  $data['name'];
            if(!$monitor->create()){
                throw new Pluf_Exception('Fail to create monitor');
            }
        }
        $this->monitor = $monitor;
    }
}
