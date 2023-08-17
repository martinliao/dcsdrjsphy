if (!function_exists('getQueryString')) {
    /********************************
     * Change Query String params
     ********************************/
    function getQueryString($filter, $merge_params = array(), $skip_params = array())
    {
        $filter = array_merge($filter, $merge_params);

        foreach ($skip_params as $param) {
            if (isset($filter[$param])) {
                unset($filter[$param]);
            }
        }

        return http_build_query($filter);
    }
}