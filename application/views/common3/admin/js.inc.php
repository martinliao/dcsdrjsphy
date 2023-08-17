
    <!-- jQuery 3 -->
    <!-- <script src="<?= HTTP_PLUGIN; ?>jquery/dist/jquery.min.js"></script> -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js" integrity="sha256-2Pmvv0kuTBOenSvLm6bvfBSSHrUJ+3A7x6P5Ebd07/g=" crossorigin="anonymous"></script>
    <!-- Bootstrap -->
    <!-- <script src="<?= HTTP_PLUGIN; ?>bootstrap-3.4.1-dist/js/bootstrap.min.js"></script> -->
    <!-- <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script> -->
    <!-- jQuery UI -->
    <!-- <script src="<?= HTTP_PLUGIN; ?>jquery-ui/jquery-ui.min.js"></script> -->
    <!-- Slimscroll
    <script src="<?= HTTP_PLUGIN; ?>jquery-slimscroll/jquery.slimscroll.min.js"></script> -->
    <!-- FastClick
    <script src="<?= HTTP_PLUGIN; ?>fastclick/lib/fastclick.js"></script> -->
    <!-- AdminLTE App
    <script src="<?= HTTP_JS; ?>adminlte.min.js"></script> -->

    <!-- fullCalendar -->
    <!-- <script src="<?= HTTP_PLUGIN; ?>moment/moment.js"></script> -->

    
    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?=HTTP_PLUGIN;?>metisMenu/dist/metisMenu.min.js"></script>
    <!-- Add fancyBox main JS and CSS files -->
    <script type="text/javascript" src="<?=HTTP_PLUGIN;?>fancybox/jquery.fancybox.js?v=2.1.5"></script>
    <script type="text/javascript" src="<?=HTTP_PLUGIN;?>toastr/toastr.min.js"></script>

    <!-- Multi Modal -->
    <script src="<?=HTTP_JS;?>multiModal.js"></script>

    <!-- <script src="http://localhost:8080/reactadmin/assets/plugins/toastr/toastr.min.js"></script>
    <script src="http://localhost:8080/reactadmin/assets/plugins/sweetalert2/sweetalert2.min.js"></script> -->

    <? if (!empty($site_js)) : ?>
		<? foreach ($site_js as $js) : ?>
		    <script type="text/javascript" src="<?=base_url() . $js;?>"></script>
		<? endforeach; ?>
	<? endif; ?>

    <script type="text/javascript">
        var _json = { _ALERT : {} };
        <?php if (isset($_JSON)): ?>
            _json = <?=json_encode($_JSON);?>;
        <?php endif; ?>
        var CI = CI || _json || {};
        $(document).ready(function(){
            $("a[rel=fancybox_group]").fancybox({
                prevEffect : 'none',
                nextEffect : 'none',
                closeBtn  : true,
            });
        });
    </script>
    <!-- foot -->
    <!-- <script src="<?= HTTP_PLUGIN; ?>datepicker/js/jquery-ui-datepicker.js"></script> -->
    <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
        });
    </script>