<style>
    .table>thead>tr>th.vert-align {
        vertical-align: middle;
    }

    th.rotated-text {
        position: relative;
        height: 140px;
        white-space: nowrap;
        padding: 0 !important;
    }

    th.rotated-text>div {
        position: absolute;
        top: 100%;
        left: 50%;
        transform: rotate(-90deg) translateY(-50%);
        transform-origin: 0 0;
    }

    th.rotated-text>div>span {
        display: inline-block;
        padding: 0px 15px;
        padding-left: 5px;
    }

    .table>tbody>tr>td {
        white-space: nowrap;
    }
</style>

<div class="card-header">
</div>
<!-- <div class="card-body pad table-responsive"> -->
<table class="table table-bordered table-sm" id="available_table" width="100%">
    <thead>
        <tr>
            <th>No</th>
            <th>ID</th>
            <th>場地名稱</th>
            <th>容納人數</th>
            <?php foreach ($period as $day) : ?>
                <th class="rotated-text">
                    <div><span><?= $day ?></span></div>
                </th>
            <?php endforeach ?>
        </tr>
    </thead>
    <tbody id="available_data">
    </tbody>
</table>