        <footer class="main-footer">
            <strong><?= $ss_settings['footer_left'] ?></strong>
            <div class="float-right d-none d-sm-inline-block">
                <b><?= $ss_settings['footer_right'] ?></b>
                <?php echo  (ENVIRONMENT === 'development') ?  'CodeIgniter Version <strong>' . CI_VERSION . '</strong>' : '' ?>
            </div>
        </footer>
    </div>
    <!-- /.toggled -->
<!-- js -->
<?= $js ?>
<!-- /js -->
<!-- standard_footer_html -->
<?= $standard_footer_html ?>
<!-- /standard_footer_html -->
</body>
</html>