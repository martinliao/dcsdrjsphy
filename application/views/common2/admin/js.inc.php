
    <!-- jQuery -->
    <!--script src="<?=HTTP_PLUGIN;?>jquery-1.12.4.min.js"></script-->
    <script src="<?=PATH_ASSETS; ?>plugins/jquery/jquery.min.js"></script>
    <!-- Bootstrap Core JavaScript -->
    <!--script src="<?=HTTP_PLUGIN;?>bootstrap/dist/js/bootstrap.min.js"></script-->
    <!-- Latest compiled and minified JavaScript -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@3.4.1/dist/js/bootstrap.min.js" integrity="sha384-aJ21OjlMXNL5UyIl/XNwTMqvzeRMZH2w8c5cRVpzpU8Y5bApTppSuUkhZXN0VxHd" crossorigin="anonymous"></script>

    <!-- Metis Menu Plugin JavaScript -->
    <script src="<?=HTTP_PLUGIN;?>metisMenu/dist/metisMenu.min.js"></script>
    <!-- Noty jquery notification plugin -->
    <!--script src="<?=HTTP_PLUGIN;?>select2/select2.full.js"></script-->
    <script src="<?=HTTP_PLUGIN;?>noty/packaged/jquery.noty.packaged.min.js"></script>
    <!-- Add mousewheel plugin (this is optional) -->
    <script type="text/javascript" src="<?=HTTP_PLUGIN;?>jquery.mousewheel-3.0.6.pack.js"></script>
    <!-- Add fancyBox main JS and CSS files -->
    <script type="text/javascript" src="<?=HTTP_PLUGIN;?>fancybox/jquery.fancybox.js?v=2.1.5"></script>

    <? if (!empty($site_js)) : ?>
		<? foreach ($site_js as $js) : ?>
		    <script type="text/javascript" src="<?=base_url() . $js;?>"></script>
		<? endforeach; ?>
	<? endif; ?>

    <script type="text/javascript">
        // http://stackoverflow.com/questions/2420970/how-can-i-get-selector-from-jquery-object/15623322#15623322
        !function(e,t){var n=function(e){var n=[];for(;e&&e.tagName!==t;e=e.parentNode){if(e.className){var r=e.className.split(" ");for(var i in r){if(r.hasOwnProperty(i)&&r[i]){n.unshift(r[i]);n.unshift(".")}}}if(e.id&&!/\s/.test(e.id)){n.unshift(e.id);n.unshift("#")}n.unshift(e.tagName);n.unshift(" > ")}return n.slice(1).join("")};e.fn.getSelector=function(t){if(true===t){return n(this[0])}else{return e.map(this,function(e){return n(e)})}}}(window.jQuery)

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
    <script src="<?= HTTP_PLUGIN; ?>moment-with-locales.js"></script>
    <script src="<?= HTTP_PLUGIN; ?>jStarbox/jstarbox.js"></script>
    <script src="<?= HTTP_PLUGIN; ?>datepicker/js/jquery-ui-datepicker.js"></script>
    <script src="<?= HTTP_JS; ?>my.js"></script>
    <script src="<?= HTTP_JS; ?>common.js"></script>
    <!-- Block UI -->
    <script src="<?= HTTP_PLUGIN; ?>jquery.blockUI-2.7.0/jquery.blockUI.js"> </script>
    <!-- sidebar anime -->
    <script>
        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("active");
        });
    </script>