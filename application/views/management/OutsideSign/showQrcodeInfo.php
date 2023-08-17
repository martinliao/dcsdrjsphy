<div class="row">
    <div class="col-lg-12">
        <div class="panel panel-default">
            <div class="panel&#45;heading">
                <i class="fa fa-list fa-lg"></i> 台北通QR-CODE
            </div>

            <div class="panel-body">
                <center>
                    <p style="font-size: 36px;font-weight:bold"><?=$class_info?></p>
                    <p style="font-size: 36px;font-weight:bold"><?=$course_date?></p>
                    <img id="qrpng" src="<?php echo htmlspecialchars($file, ENT_HTML5|ENT_QUOTES).'?='.time()?>">
                    <p style="font-size: 36px;font-weight:bold">請使用台北通APP掃描QR-CODE進行簽到</p>
                </center>
            </div>
        </div>
    </div>
</div>