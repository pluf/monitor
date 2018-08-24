<?php

/**
 * Monitor tag
 *
 * @author maso <mostafa.barmshory@dpq.co.ir>
 * @author hadi <mohammad.hadi.mansouri@dpq.co.ir>
 *
 */
class Monitor_Tag extends Pluf_Model
{

    /**
     *
     * {@inheritdoc}
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'monitor_tags';
        $this->_a['cols'] = array(
            'id' => array(
                'type' => 'Pluf_DB_Field_Sequence',
                'blank' => true,
                'editable' => false,
                'readable' => true
            ),
            'name' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'is_null' => false,
                'unique' => true,
                'size' => 100,
                'editable' => false,
                'readable' => true
            ),
            'description' => array(
                'type' => 'Pluf_DB_Field_Varchar',
                'is_null' => true,
                'size' => 250,
                'editable' => true,
                'readable' => true
            ),
            'creation_dtime' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'is_null' => true,
                'editable' => false,
                'readable' => true
            ),
            'modif_dtime' => array(
                'type' => 'Pluf_DB_Field_Datetime',
                'is_null' => true,
                'editable' => false,
                'readable' => true
            ),
            /*
             * Relations
             */
            'metrics' => array(
                'type' => 'Pluf_DB_Field_Manytomany',
                'model' => 'Monitor_Metric',
                'is_null' => true,
                'readable' => false,
                'editable' => false,
                'relate_name' => 'tags'
            )
        );
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
     * Sets initial associations
     *
     * @param array $relations
     */
    function initRelations($relations)
    {
        foreach ($relations as $rel) {
            $model = new Monitor_Tag();
            $sql = new Pluf_SQL('name=%s', array(
                $rel['tag']
            ));
            $monitorTag = $monitorTag->getOne($sql->gen());
        }
    }
}
