## gen_virtualhost

1. copy gen_virtualhost.sh 到 home 目錄
2. 用以下指令產生 nginx virtualhost
    ```
    DOCROOT='/var/www/html/ci3' PROJECTID='ci3' sshUsername='vagrant' bash ~/gen_virtualhost.sh
    ```
3. 重啟 nginx
    ```
    { sudo nginx -t; } && { sudo systemctl restart nginx; sudo systemctl restart php7.3-fpm; }
    ```

## CI3 路由 route, memo

https://github.com/tasmanwebsolutions/CI3-default_controller_route_with_sub_folder/blob/master/application/core/MY_Router.php