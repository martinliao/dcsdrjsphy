<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

/**
 * MY_Model, 從FET版本而來, 沒有修改.(HMVC,SmartACL都不需要)
 */
class MY_Model extends CI_Model
{

    protected $table;
    protected $pk;

    public function __construct()
    {
        parent::__construct();
    }

    protected function init($table, $pk)
    {
        $this->table = $table;
        $this->pk = $pk;
    }

    protected function setDb($DB)
    {
        $this->db = $this->load->database($DB, TRUE);
    }

    public function insert($fields = array(), $now_field = NULL)
    {
        if ($now_field) {
            $this->db->set($now_field, 'now()', false);
        }

        $this->db->insert($this->table, $fields);
        return $this->db->insert_id();
    }

    public function update($conditions = NULL, $fields = array())
    {
        if ($conditions) {
            if (is_array($conditions)) {
                $this->db->where($conditions);
            } else {
                $this->db->where($this->pk, $conditions);
            }
        }

        return $this->db->update($this->table, $fields);
        // return $this->db->affected_rows();
    }

    public function update_or_create($conditions = array(), $fields = array())
    {
        $result = array(
            'status' => FALSE,
            'created' => FALSE,
        );

        $data = $this->get($conditions);
        if ($data) {
            $rs = $this->update($conditions, $fields);
            if ($rs) {
                $result['status'] = TRUE;
            }
        } else {
            if (!is_array($conditions)) {
                $conditions = array($this->pk => $conditions);
            }
            $fields = array_merge($conditions, $fields);
            $saved_id = $this->insert($fields);
            if ($saved_id !== FALSE) {
                $result['created'] = TRUE;
                $result['saved_id'] = $saved_id;
                $result['status'] = TRUE;
            }
        }

        return $result;
    }


    public function delete($conditions = NULL)
    {
        if ($conditions) {
            if ($conditions && is_array($conditions)) {
                $this->db->where($conditions);
            } else {
                $this->db->where($this->pk, $conditions);
            }
        }

        $result = $this->db->delete($this->table);

        return $this->_result($result);
    }

    public function exists($conditions = array(), $onlyOne = FALSE)
    {
        $count = $this->getCount($conditions);
        if ($onlyOne) {
            return ($count == 1);
        }
        return ($count > 0);
    }

    public function getCount($conditions = array())
    {
        $this->db->from($this->table);
        $this->db->where($conditions);
        return $this->db->count_all_results();
    }

    public function getSum($field = NULL, $conditions = array())
    {
        $this->db->select_sum($field, 'sum');
        $query = $this->db->get_where($this->table, $conditions);
        $rs = $query->row();
        return ($rs->sum) ? $rs->sum : 0;
    }

    public function get($conditions = NULL, $type = 'array')
    {
        $this->db->from($this->table);
        if ($conditions) {
            if (is_array($conditions)) {
                $this->db->where($conditions);
            } else {
                $this->db->where($this->pk, $conditions);
            }
        }
        $query = $this->db->get();
        return ($type == 'object') ? $query->row() : $query->row_array();
    }

    public function getAll($order_by = NULL, $type = 'array')
    {
        if ($order_by) {
            $this->db->order_by($order_by);
        }
        $query = $this->db->get($this->table);
        return ($type == 'object') ? $query->result() : $query->result_array();
    }

    public function getData($params = array(), $type = 'array', $returncount = false)
    {
        //jd($params);
        $params = array_merge(
            array(
                'select' => '*',
                'conditions' => array(),
                'rows' => NULL,
                'offset' => NULL,
                'order_by' => NULL,
                'group_by' => NULL,
                'like' => NULL,
                'or_like' => NULL,
                'where_in' => NULL,
                'or_where_in' => NULL,
                'where_not_in' => NULL,
                'or_where_not_in' => NULL,
                'join' => NULL,
            ),
            $params
        );

        $this->db->select($params['select']);

        if (isset($params['table']) && !empty($params['table'])) {
            $this->db->from($params['table']);
        } else {
            $this->db->from($this->table);
        }

        if (isset($params['join'])) {
            foreach ($params['join'] as $row) {
                if ($row['join_type'] == '') {
                    $this->db->join($row['table'], $row['condition']);
                } else {
                    $this->db->join($row['table'], $row['condition'], $row['join_type']);
                }
            }
            //$this->db->join($params['join']['table'], $params['join']['condition']);
        }
        $this->db->where($params['conditions']);

        if (isset($params['escape_query'])) {
            $this->db->where($params['escape_query']['statusSql']);
        }

        if ($params['rows'] && $params['offset']) {
            $this->db->limit($params['rows'], $params['offset']);
        } elseif ($params['rows']) {
            $this->db->limit($params['rows']);
        }

        if (isset($params['like'])) {
            if (isset($params['like']['many']) && $params['like']['many'] == TRUE) {
                foreach ($params['like']['data'] as $k => $row) {
                    $this->db->like($row['field'], $row['value'], $row['position']);
                }
            } else {
                $this->db->like($params['like']['field'], $params['like']['value'], $params['like']['position']);
            }
        }

        if (isset($params['or_like'])) {
            if (isset($params['or_like']['many']) && $params['or_like']['many'] == TRUE) {
                $sql = '';
                foreach ($params['or_like']['data'] as $k => $row) {
                    $search_str = $this->db->escape_like_str($row['value']);
                    switch ($row['position']) {
                        case 'both':
                            $search_str = '%' . $search_str . '%';
                            break;
                        case 'left':
                            $search_str = '%' . $search_str;
                            break;
                        case 'right':
                            $search_str = $search_str . '%';
                            break;
                    }

                    if ($k != 0) {
                        $sql .= ' OR ';
                    }

                    $sql .= $row['field'] . ' LIKE \'' . $search_str . '\'';
                    // ===================
                    // if ($k == 0) {
                    //     $this->db->like($row['field'], $row['value'], $row['position']);
                    // } else {
                    //     $this->db->or_like($row['field'], $row['value'], $row['position']);
                    // }
                }
                $this->db->where('(' . $sql . ')');
            } else {
                $this->db->or_like($params['or_like']['field'], $params['or_like']['value'], $params['or_like']['position']);
            }
        }

        if (isset($params['group_by'])) {
            $this->db->group_by($params['group_by']);
        }

        if (isset($params['order_by'])) {
            $this->db->order_by($params['order_by']);
        }

        if (isset($params['where_in'])) {
            $this->db->where_in($params['where_in']['field'], $params['where_in']['value']);
        }

        if (isset($params['where_special'])) {
            $this->db->where($params['where_special']);
        }

        if (isset($params['or_where_in']['field']) && isset($params['or_where_in']['value'])) {
            $this->db->or_where_in($params['or_where_in']['field'], $params['or_where_in']['value']);
        }

        if (isset($params['where_not_in']['field']) && isset($params['where_not_in']['value'])) {
            $this->db->where_not_in($params['where_not_in']['field'], $params['where_not_in']['value']);
        }

        if (isset($params['or_where_not_in'])) {
            $this->db->or_where_not_in($params['or_where_not_in']['field'], $params['or_where_not_in']['value']);
        }
        /*2019/10/02*/
        if (isset($params['distinct'])) {
            $this->db->distinct($params['distinct']);
        }

        if (isset($params['and_like'])) {
            if (isset($params['and_like']['many']) && $params['and_like']['many'] == TRUE) {
                $sql = '';
                foreach ($params['and_like']['data'] as $k => $row) {
                    $search_str = $row['value'];
                    switch ($row['position']) {
                        case 'both':
                            $search_str = '%' . $search_str . '%';
                            break;
                        case 'left':
                            $search_str = '%' . $search_str;
                            break;
                        case 'right':
                            $search_str = $search_str . '%';
                            break;
                    }

                    if ($k != 0) {
                        $sql .= ' AND ';
                    }

                    $sql .= $row['field'] . ' LIKE \'' . $search_str . '\'';
                    // ===================
                    // if ($k == 0) {
                    //     $this->db->like($row['field'], $row['value'], $row['position']);
                    // } else {
                    //     $this->db->or_like($row['field'], $row['value'], $row['position']);
                    // }
                }
                $this->db->where('(' . $sql . ')');
            } else {
                $this->db->or_like($params['and_like']['field'], $params['and_like']['value'], $params['and_like']['position']);
            }
        }

        if ($returncount) {
            return $this->db->count_all_results();
        }

        $query = $this->db->get();

        // jd($this->db->last_query());
        return ($type == 'object') ? $query->result() : $query->result_array();
    }

    protected function _result($rs)
    {
        if ($rs) {
            $result = array('status' => TRUE);
        } else {
            $result = array(
                'status' => FALSE,
                'errNo' => $this->db->_error_number(),
                'errMsg' => $this->db->_error_message(),
            );
        }

        return $result;
    }

    public function paginate()
    {
        $controller = &get_instance();
        $sql = $this->db->get_compiled_select();
        $counter = $this->db->query("SELECT COUNT(*) count FROM ($sql) c");
        $counter = $counter->row();
        $config['total_rows'] = $counter->count;
        // dd($config['total_rows']);
        // dd($this->db->last_query());
        $page = $controller->getFilterData('page', 1);
        $per_page = $controller->getFilterData('rows', 10);

        $this->db->limit($per_page, ($page - 1) * $per_page);
        $config['per_page'] = $per_page;
        $controller->data['paginate_config'] = $config;
        $controller->pagination->initialize($controller->data['paginate_config']);
    }
}
