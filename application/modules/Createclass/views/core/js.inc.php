
<script type="text/javascript">
function checkSave(){
    if(document.getElementById('is_mixed').value == '1'){
        if($('#online_course table tbody tr').size() == 0){
            alert('線上課程至少1門');
            return false;
        }
    }

    if(document.getElementById('class_no').value == ''){
        alert('班期代碼不能為空');
        return false;
    }

    var obj = document.getElementById('data-form');
    obj.submit();
}
</script>