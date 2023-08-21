var cdnBase = 'https://cdn.datatables.net';
var require = {
    baseUrl : '[BASEURL]',
    // We only support AMD modules with an explicit define() statement.
    enforceDefine: true,
    skipDataMain: true,
    waitSeconds : 0,

    paths: {
        jquery: '[JSURL]lib/jquery/jquery-3.6.0[JSMIN][JSEXT]',
        jqueryui: '[JSURL]lib/jquery/ui-1.13.2/jquery-ui[JSMIN][JSEXT]',
        jqueryuicss: '[JSURL]lib/jquery/ui-1.13.2/jquery-ui',

        //'datatables.net': cdnBase + '/1.13.4/js/jquery.dataTables.min',
        'datatables.net': '[JSURL]lib/DataTables/jquery.dataTables[JSMIN][JSEXT]',
        //bootstrapDataTablesCSS: '[JSURL]lib/DataTables/dataTables.bootstrap',
        //bootstrapDataTables: '[JSURL]lib/DataTables/dataTables.bootstrap[JSMIN][JSEXT]', // 需要 datatables.net
        //datatablescss: '[JSURL]lib/DataTables/datatables',
        //datatables: '[JSURL]lib/DataTables/datatables[JSMIN][JSEXT]',
        bootstrapdataTablescss: '[JSURL]lib/DataTables/dataTables.bootstrap',
        jquerydataTables: '[JSURL]lib/DataTables/jquery.dataTables[JSMIN][JSEXT]',
        datatables: '[JSURL]lib/DataTables/dataTables.bootstrap[JSMIN][JSEXT]',

        moment: '[JSURL]lib/moment/moment[JSMIN][JSEXT]',
        daterangepicker: '[JSURL]lib/jquery/daterangepicker/daterangepicker[JSMIN][JSEXT]',
        'daterangepicker-css': '[JSURL]lib/jquery/daterangepicker/daterangepicker',
        fullcalendar: '[JSURL]lib/jquery/fullcalendar/fullcalendar[JSMIN][JSEXT]',
        fullcalendarcss: '[JSURL]lib/jquery/fullcalendar/fullcalendar',
        jqueryprivate: '[JSURL]lib/requirejs/jquery-private[JSEXT]'
    },

    // Custom jquery config map.
    map: {
      // '*' means all modules will get 'jqueryprivate' for their 'jquery' dependency.
      '*': { jquery: 'jqueryprivate' },
      // Stub module for 'process'. This is a workaround for a bug in MathJax (see MDL-60458).
      '*': { process: 'core/first' },
      '*': {
          'css': '[JSURL]lib/require-css/css.min.js'
      },

      // 'jquery-private' wants the real jQuery module though. 
      // If this line was not here, there would be an unresolvable cyclic dependency.
      jqueryprivate: { jquery: 'jquery' }
    },
    shim: {
        jqueryui: { deps: ['jquery', 'css!jqueryuicss']},

        //'jqueryDataTables': {deps: ['jquery']},
        //'bootstrapDataTables' : {deps: ['jquery', 'datatables.net', 'css!bootstrapDataTablesCSS' ]},
        'jquerydataTables': {deps: ['jquery']},
        'datatables' : {deps: ['jquery', 'jquerydataTables', 'css!bootstrapdataTablescss' ]},

        'daterangepicker' : {deps: ['jquery', 'css!daterangepicker-css']},
        'fullcalendar' : {
            deps: ['jquery', 'jqueryui'] //, 'css!fullcalendarcss'
        }
    }
};
