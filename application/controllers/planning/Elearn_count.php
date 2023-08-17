<?php if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

class Elearn_count extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === false) {
            redirect(base_url('welcome'));
        }

        $this->load->model('planning/elearn_count_model');

        if (!isset($this->data['filter']['page'])) {
            $this->data['filter']['page'] = '1';
        }
        if (!isset($this->data['filter']['sort'])) {
            $this->data['filter']['sort'] = '';
        }
        if (!isset($this->data['filter']['query_year'])) {
            $this->data['filter']['query_year'] = date('Y') - 1911;
        }
    }

    public function index()
    {
        $this->data['page_name'] = 'list';
        $conditions = array();

        if ($this->data['filter']['query_year'] !== '') {
            $conditions['year'] = $this->data['filter']['query_year'];
        }

        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];

        $attrs = array(
            'conditions' => $conditions,
        );

        $this->data['filter']['total'] = $total = $this->elearn_count_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page - 1) * $rows;

        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );

        $this->data['list'] = $this->elearn_count_model->getList($attrs);
        /*echo"<pre>";
        var_dump($this->data['list']);
        die();*/

        $this->load->library('pagination');
        $config['base_url'] = base_url("planning/elearn_count?" . $this->getQueryString(array(), array('page')));
        $config['total_rows'] = $total;
        $config['per_page'] = $rows;
        $this->pagination->initialize($config);
        $this->data['link_refresh'] = base_url("planning/elearn_count/");
        $this->data['link_detail'] = base_url("planning/elearn_count/");
        $this->data['link_export'] = base_url("planning/elearn_count/export");
        $this->layout->view('planning/elearn_count/list', $this->data);
    }

    public function export()
    {
        $conditions = array();
        if ($this->data['filter']['query_year'] !== '') {
            $conditions['year'] = $this->data['filter']['query_year'];
        }
        $page = $this->data['filter']['page'];
        $rows = $this->data['filter']['rows'];
        $attrs = array(
            'conditions' => $conditions,
        );
        $this->data['filter']['total'] = $total = $this->elearn_count_model->getListCount($attrs);
        $this->data['filter']['offset'] = $offset = ($page - 1) * $rows;
        $attrs = array(
            'conditions' => $conditions,
            'rows' => $rows,
            'offset' => $offset,
        );
        $info = $this->elearn_count_model->getList($attrs);
        header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=Elearn.csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        $filename = 'Elearn.csv';
        echo iconv("UTF-8", "BIG5", "編號,");
        echo iconv("UTF-8", "BIG5", "機關名稱,");
        echo iconv("UTF-8", "BIG5", "班期名稱,");
        echo iconv("UTF-8", "BIG5", "訓練期數,");
        echo iconv("UTF-8", "BIG5", "訓練人數(每期),");
        echo iconv("UTF-8", "BIG5", "訓練人數(總計),");
        echo iconv("UTF-8", "BIG5", "每次課程時數,");
        echo iconv("UTF-8", "BIG5", "辦班時間(月份),");
        echo iconv("UTF-8", "BIG5", "實境錄製教材(單一主題),");
        echo iconv("UTF-8", "BIG5", "實境錄製教材(系列性主題),");
        echo iconv("UTF-8", "BIG5", "全動畫教材(貴局處無經費),");
        echo iconv("UTF-8", "BIG5", "全動畫教材(貴局處有經費),\r\n");
        $k=1;
        for ($i = 0; $i < count($info); $i++) {
            echo "\"" . iconv("UTF-8", "BIG5",  $k. "\",");
            echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['bname'] . "\",");
            echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['class_name'] . "\",");
            echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['term'] . "\",");
            echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['no_persons'] . "\",");
            echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['no_persons'] * $info[$i]['term'] . "\",");
            echo "\"" . iconv("UTF-8", "BIG5", $info[$i]['range'] . "\",");

            $mon = array();
            $mon[0] = $info[$i]["start_date1"];
            $mon[1] = $info[$i]["start_date2"];
            $mon[2] = $info[$i]["start_date3"];
            $mon[3] = $info[$i]["end_date1"];
            $mon[4] = $info[$i]["end_date2"];
            $mon[5] = $info[$i]["end_date3"];
            for ($ii = 0; $ii < count($mon); $ii++) {
                if (strlen($mon[$ii] < 10)) {
                    $mon[$ii] = -1;
                } else {
                    $mon[$ii] = (int) date("m", strtotime($mon[$ii]));
                }
            }
            $mon = array_unique($mon);
            $monStr = "";
            foreach ($mon as $aa) {
                if ($aa != -1) {
                    $monStr .= $aa . ",";
                }

            }
            $show = substr($monStr, 0, strlen($monStr) - 1);
            echo "\"" . iconv("UTF-8", "BIG5", $show . "\",");

            
            if ((empty($info[$i]['course_zero'] && empty($info[$i]['course_one']) && empty($info[$i]['course_two']) && empty($info[$i]['course_three']))) && !$info[$i]['course_flag']) {
                $course = '課程規劃中';
                echo "\"" . iconv("UTF-8", "BIG5", "" . "\",");
                echo "\"" . iconv("UTF-8", "BIG5", $course . "\",");
                echo "\"" . iconv("UTF-8", "BIG5", "" . "\",");
                } else {
                if (!empty($info[$i]['course_zero'])) {
                    echo "\"" . iconv("UTF-8", "BIG5", "V" . "\",");
                }
                
                if (!empty($info[$i]['course_one'])) {
                    echo "\"" . iconv("UTF-8", "BIG5", "V" . "\",");
                }
                
                if (!empty($info[$i]['course_two'])) {
                    echo "\"" . iconv("UTF-8", "BIG5", "V" . "\",");
                }
            }
            if (!empty($info[$i]['course_three'])) {
                echo "\"" . iconv("UTF-8", "BIG5", "V" . "\",");
            }

            echo "\"" . iconv("UTF-8", "BIG5", ''. "\"\r\n");
            $k++;
        }
    }

}
