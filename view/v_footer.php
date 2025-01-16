<script src="../assets/jquery/jquery.min.js?v=<?php echo $version; ?>"></script>
<!-- <script src="../assets/jquery-ui/jquery-ui.min.js"></script> -->
<!-- <script src="../assets/jquery-ui/jquery-ui.multidatespicker.js"></script> -->
<script src="../assets/sidebarComponents/sidebar.js?v=<?php echo $version; ?>"></script>
<script src="../assets/bootstrap/js/bootstrap.bundle.min.js?v=<?php echo $version; ?>"></script>
<script src="../assets/dayjs/dayjs.min.js?v=<?php echo $version; ?>"></script>
<script src="../assets/dayjs/plugin/duration.js?v=<?php echo $version; ?>"></script>
<script src="../assets/dayjs/plugin/customParseFormat.js?v=<?php echo $version; ?>"></script>
<script src="../assets/dayjs/plugin/buddhistEra.js?v=<?php echo $version; ?>"></script>
<script src="../assets/dayjs/plugin/relativeTime.js?v=<?php echo $version; ?>"></script>
<script src="../assets/dayjs/locale/th.js?v=<?php echo $version; ?>"></script>
<script src="../assets/Sortable/sortablejs/Sortable.min.js?v=<?php echo $version; ?>"></script>
<script src="../assets/Sortable/jquery-sortablejs/jquery-sortable.js?v=<?php echo $version; ?>"></script>
<script src="../assets/js/sort-function.js?v=<?php echo $version; ?>"></script>
<script src="../assets/chartjs/chart.umd.min.js"></script>
<script src="../assets/chartjs/chartjs-plugin-datalabels.min.js"></script>
<script>
    dayjs.extend(window.dayjs_plugin_duration);
    dayjs.extend(window.dayjs_plugin_customParseFormat);
    dayjs.extend(window.dayjs_plugin_buddhistEra);
    dayjs.extend(window.dayjs_plugin_relativeTime);
    dayjs.locale('th');
</script>
<script src="../assets/DataTables/datatables.min.js?v=<?php echo $version; ?>"></script>
<script src=../assets/js/jszip.min.js?v=<?php echo $version; ?>"></script>
<!-- <script src="../assets/datatable/buttons.bootstrap5.min.js?v=<?php echo $version; ?>"></script>
<script src="../assets/datatable/buttons.html5.min.js?v=<?php echo $version; ?>"></script>
<script src="../assets/datatable/buttons.print.min.js?v=<?php echo $version; ?>"></script> -->
<!-- <script src="../assets/aos/aos.js?v=<?php echo $version; ?>"></script> -->
<!-- <script src="../assets/swiper/swiper-bundle.min.js?v=<?php echo $version; ?>"></script> -->
<script src="../assets/js/main.js?v=<?php echo $version; ?>"></script>
<!-- <script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script> -->
<script src="../assets/momentjs/moment.js"></script>
<script src="../assets/daterangepicker/daterangepicker.js"></script>

<script>
    $(document).ready(function(){
        let menuheight = ($(window).height() - 227);
        $("#menubar").css("height",menuheight+"px");
        $("#menubar").css("overflow","auto");
    });
</script>
</body>
</html>