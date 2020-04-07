<?php

/**
 * Monitor
 *
 * @author maso <mostafa.barmshory@dpq.co.ir>
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *
 */
class Monitor_Metric extends Pluf_Model
{

    /**
     *
     * {@inheritdoc}
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'monitor_metrics';
        $this->_a['cols'] = array(
            'id' => array(
                'type' => 'Sequence',
                'blank' => true,
                'editable' => false,
                'readable' => true
            ),
            'name' => array(
                'type' => 'Varchar',
                'is_null' => false,
                'size' => 100,
                'unique' => true,
                'editable' => false,
                'readable' => true
            ),
            'description' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'size' => 250,
                'editable' => true,
                'readable' => true
            ),
            'value' => array(
                'type' => 'Float',
                'is_null' => true,
                'is_null' => true,
                'default' => 0.0,
                'editable' => false,
                'readable' => true
            ),
            'unit' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'size' => 100,
                'editable' => false,
                'readable' => true
            ),

            'function' => array(
                'type' => 'Varchar',
                'is_null' => true,
                'size' => 100,
                'editable' => false,
                'readable' => false
            ),
            'interval' => array(
                'type' => 'Integer',
                'is_null' => true,
                'editable' => false,
                'readable' => false
            ),
            'cacheable' => array(
                'type' => 'Boolean',
                'is_null' => true,
                'defualt' => false,
                'editable' => false,
                'readable' => false
            ),
            'modif_dtime' => array(
                'type' => 'Datetime',
                'is_null' => true,
                'editable' => false,
                'readable' => true
            )
            // Relations
        );
    }

    public function loadViews(): array
    {
        $engine = $this->getEngine();
        $schema = $engine->getSchema();
        
        $tag = new Monitor_Tag();

        // Assoc. table
        $tag_asso = $schema->getRelationTable($this, $tag);
        $t_metric = $schema->getTableName($this);
        $metric_fk = $schema->getAssocField($this);

        return array(
            'join_tag' => array(
                'join' => 'LEFT JOIN ' . $tag_asso . ' ON ' . $t_metric . '.id=' . $metric_fk
            )
        );
    }

    /**
     * Call monitor property and get value
     *
     * @return object
     */
    function invoke()
    {
        // Get old value
        if ($this->cacheable) {
            $now = new DateTime('now');
            $last = new DateTime($this->modif_dtime);
            $diff = $now->getTimestamp() - $last->getTimestamp();
            $interval = $this->interval;
            if ($interval == null || $interval == 'undefined') {
                $interval = 36000; // 1 day
            }
            if ($diff <= $interval) {
                return $this->value;
            }
        }
        // Get new value
        $request = null;
        if (array_key_exists('_PX_request', $GLOBALS)) {
            $request = $GLOBALS['_PX_request'];
        }
        $match = array(
            'property' => $this->name,
            'metricName' => $this->name
        );
        $result = call_user_func_array(explode('::', $this->function), array(
            $request,
            $match
        ));
        $this->value = $result;
        if ($this->cacheable && ! $this->update()) {
            throw new \Pluf\Exception('Fail to update model');
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
}
