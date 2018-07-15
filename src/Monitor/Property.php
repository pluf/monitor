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
            
            'value' => array(
                'type' => 'Pluf_DB_Field_Float',
                'blank' => true,
                'is_null' => true,
                'default' => 0.0,
                'editable' => false,
                'readable' => true
            ),
            'unit' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'blank' => true,
                'size' => 100,
                'editable' => false,
                'readable' => true
            ),
            'interval' => array(
                'type' => 'Pluf_DB_Field_Integer',
                'blank' => true,
                'editable' => false,
                'readable' => false
            ),
            'cacheable' => array(
                'type' => 'Pluf_DB_Field_Boolean',
                'blank' => true,
                'defualt' => false,
                'editable' => false,
                'readable' => false
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
                'relate_name' => 'properties',
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
    }

    /**
     * Call monitor property and get value
     *
     * @param Pluf_HTTP_Request $params
     * @return object
     */
    function invoke($request, $match = array())
    {
        // Get old value
        if ($this->cacheable) {
            $now = new DateTime('now');
            $last = new DateTime($this->modif_dtime);
            $diff = $now->getTimestamp() - $last->getTimestamp();
            $interval = $this->interval;
            if($interval == null || $interval == 'undefined'){
                $interval = 3600000;
            }
            if ($diff <= $interval) {
                return $this->value;
            }
        }
        // Get new value
        $match['property'] = $this->name;
        $result = call_user_func_array(explode('::', $this->function), array(
            $request,
            $match
        ));
        $this->value = $result;
        if ($this->cacheable && ! $this->update()) {
            throw new Pluf_Exception('Fail to update model');
        }
        return $this->value;
    }

    /**
     *
     * {@inheritdoc}
     * @see Pluf_Model::preSave()
     */
    function preSave($create = false)
    {
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
        $sql = new Pluf_SQL('name=%s', array(
            $data['monitor']
        ));
        $monitor = $monitor->getOne($sql->gen());
        if (! isset($monitor) || $monitor->isAnonymous()) {
            $monitor = new Monitor();
            $monitor->name = $data['monitor'];
            if (! $monitor->create()) {
                throw new Pluf_Exception('Fail to create monitor');
            }
        }
        $this->monitor = $monitor;
    }
}
