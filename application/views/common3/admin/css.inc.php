    <!-- Custom Fonts -->
    <link type="text/css" href="<?=HTTP_PLUGIN;?>font-awesome/css/font-awesome.min.css" rel="stylesheet">
    <!-- Bootstrap Core CSS -->
    <!-- <link href="<?= HTTP_PLUGIN; ?>bootstrap-3.4.1-dist/css/bootstrap.min.css" rel="stylesheet"> -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap.min.css" integrity="sha384-HSMxcRTRxnN+Bdg0JdbxYKrThecOKuH5zCYotlSAcp1+c8xmyTe9GYg1l9a69psu" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/css/bootstrap-theme.min.css" integrity="sha384-6pzBo3FDv/PJ8r2KRkGHifhEocL+1X2rVCTTkUfGk7/0pbek5mMa1upzvWbrUbOZ" crossorigin="anonymous">

    <!-- <link href="<?= HTTP_PLUGIN; ?>jquery-ui-1.13.2/jquery-ui.css" rel="stylesheet"> -->

    <!-- fullCalendar
    <link rel="stylesheet" href="<?= HTTP_PLUGIN; ?>fullcalendar/dist/fullcalendar.min.css">
    <link rel="stylesheet" href="<?= HTTP_PLUGIN; ?>fullcalendar/dist/fullcalendar.print.min.css" media="print"> -->

    <!-- Theme style
    <link rel="stylesheet" href="<?= HTTP_CSS; ?>AdminLTE.min.css"> -->

    <!-- MetisMenu CSS -->
    <!-- <link href="<?=HTTP_PLUGIN;?>metisMenu/dist/metisMenu.min.css" rel="stylesheet"> -->
    <!-- Add fancyBox main JS and CSS files -->
    <!-- <link type="text/css" href="<?=HTTP_PLUGIN;?>fancybox/jquery.fancybox.css?v=2.1.5" rel="stylesheet" media="screen" /> -->
    <!-- Custom CSS 會影響 DataTable 的 sort(CSS掉圖) -->
    <!-- <link href="<?=HTTP_CSS;?>sb-admin-2.css" rel="stylesheet"> -->
    <link href="<?=HTTP_PLUGIN;?>toastr/toastr.min.css" rel="stylesheet">

    <!-- Self CSS -->
    <!-- <link href="<?=HTTP_CSS;?>style.css" rel="stylesheet"> -->

	<? if (!empty($site_css)) : ?>
		<? foreach ($site_css as $css) : ?>
			<link rel="stylesheet" type="text/css" href="<?=base_url() . $css;?>" />
		<? endforeach; ?>
	<? endif; ?>

    <!-- <link rel="stylesheet" href="http://localhost:8080/reactadmin/assets/plugins/toastr/toastr.min.css">
    <link rel="stylesheet" href="http://localhost:8080/reactadmin/assets/plugins/sweetalert2/sweetalert2.min.css"> -->