<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class ExportStudentList extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        if ($this->flags->is_login === FALSE) {
            redirect(base_url('welcome'));
        }
        $this->load->model('management/ExportStudentList_model');
    }

    public function index()
    {
        $this->data['show_class'] = base_url("management/class_record/show_class");
        $this->data['exportCSV'] = base_url("management/ExportStudentList/export");
        $this->layout->view('management/exportStudentList/list',$this->data);
    }

    public function export()
    {
    	$post = $this->input->post();
    	$filename = iconv('UTF-8', 'BIG5', "多班期學員名冊").'.csv';

    	header("Content-type: application/csv");
        header("Content-Disposition: attachment; filename=$filename");

        echo iconv('UTF-8', 'BIG5', "10E匯出多班結訓名冊\r\n");
        echo iconv('UTF-8', 'BIG5', "年度/班期名稱/期別,");
        echo iconv('UTF-8', 'BIG5', "學號,");
        echo iconv('UTF-8', 'BIG5', "服務單位,");
        echo iconv('UTF-8', 'BIG5', "職稱,");
        echo iconv('UTF-8', 'BIG5', "姓名,");
        echo iconv('UTF-8', 'BIG5', "出生年月日,");
        echo iconv('UTF-8', 'BIG5', "身分證字號,");
        echo iconv('UTF-8', 'BIG5', "電話,");
        echo iconv('UTF-8', 'BIG5', "E-MAIL,");
        echo iconv('UTF-8', 'BIG5', "男/女,");
        echo iconv('UTF-8', 'BIG5', "主管級別,");
        echo iconv('UTF-8', 'BIG5', "現支官職等,");
        echo iconv('UTF-8', 'BIG5', "學歷,");
        echo iconv('UTF-8', 'BIG5', "退休否,");
        echo iconv('UTF-8', 'BIG5', "備註\r\n");

    	$tmpCount = count($post['pkey']);
    	for($i=0;$i<$tmpCount;$i++){
    		$tmpArray = explode(',', $post['pkey'][$i]);
    		if(count($tmpArray) == 3){
    			$studentList = $this->ExportStudentList_model->getSignStudentInfo($tmpArray[0],$tmpArray[1],$tmpArray[2]);
    			$tmpCount2 = count($studentList);

    			for($j=0;$j<$tmpCount2;$j++){
    				$classInfo = $studentList[$j]['year'].'/'.$studentList[$j]['class_name'].'/第'.$studentList[$j]['term'].'期';
    				echo iconv('UTF-8', 'BIG5', $classInfo).',';
    				echo iconv('UTF-8', 'BIG5', $studentList[$j]['st_no']).',';

    				if(!empty($studentList[$j]['out_gov_name'])){
    					echo iconv('UTF-8', 'BIG5', $studentList[$j]['out_gov_name']).',';
    				} else {
    					echo iconv('UTF-8', 'BIG5', $studentList[$j]['bureau_name']).',';
    				}

    				echo iconv('UTF-8', 'BIG5', $studentList[$j]['job_title_name']).',';
    				echo iconv('UTF-8', 'BIG5', $studentList[$j]['student_name']).',';
    				echo iconv('UTF-8', 'BIG5', $studentList[$j]['birthday']).',';
    				echo iconv('UTF-8', 'BIG5', $studentList[$j]['id']).',';

    				if(!empty($studentList[$j]['co_empdb_poftel'])){
    					echo iconv('UTF-8', 'BIG5', $studentList[$j]['co_empdb_poftel']).',';
    				} else {
    					echo iconv('UTF-8', 'BIG5', $studentList[$j]['office_tel']).',';
    				}

    				echo iconv('UTF-8', 'BIG5', $studentList[$j]['office_email']).',';

    				if($studentList[$j]['gender'] == 'F'){
    					echo iconv('UTF-8', 'BIG5', '女').',';
    				} else if($studentList[$j]['gender'] == 'M'){
    					echo iconv('UTF-8', 'BIG5', '男').',';
    				} else {
    					echo ',';
    				}

    				echo iconv('UTF-8', 'BIG5', $studentList[$j]['supervisor_name']).',';
    				echo iconv('UTF-8', 'BIG5', $studentList[$j]['job_level_name']).',';
    				echo iconv('UTF-8', 'BIG5', $studentList[$j]['education_name']).',';

    				if($studentList[$j]['retirement'] == '0'){
    					echo iconv('UTF-8', 'BIG5', '是').',';
    				} else {
    					echo ',';
    				}

    				if($studentList[$j]['yn_sel'] == '4'){
    					echo iconv('UTF-8', 'BIG5', '退訓')."\r\n";
    				} else if($studentList[$j]['yn_sel'] == '5'){
    					echo iconv('UTF-8', 'BIG5', '未報到')."\r\n";
    				} else {
    					echo "\r\n";
    				}
    			}
    		}
    	
    	}
    }

}
