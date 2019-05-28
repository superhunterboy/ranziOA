环境配置
-----
```
website 10.88.10.20 
DB maria  10.88.10.38
nginx php5.4 apache2.4 mariadb
nginx 反向代理 httpd 127.0.0.1:8081
php 插件
php-ladp php-pdo php-zip
```
权限
-----
```
登陆账户  gosling 秘钥对方式登陆，端口22
部署位置 /home/gosling/websites/oa
config, tmp 目录为 apache 权限
其他目录为当前账户权限

```
nginx 配置文件 
-----
 /etc/nginx/site-enabled/oa.conf 
```
server {
   
   listen 80;
   root /home/gosling/websites/empty;
   server_name oa.nyjt88.com;
   
   location / {
       proxy_pass http://127.0.0.1:8081/;
       proxy_set_header Remoteip $http_remoteip;
       proxy_set_header Host $http_host;
       proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
   }
 
   location ~ /\.ht {
       deny  all;
   }

}
```
httpd  配置文件
-----
```
httpd.conf
<Directory "/home/gosling/websites/oa/www">
    AllowOverride None
    # Allow open access:
    Require all granted
</Directory>

# Further relax access to the default document root:
<Directory "/home/gosling/websites/oa/www">
    #
    # Possible values for the Options directive are "None", "All",
    # or any combination of:
    #   Indexes Includes FollowSymLinks SymLinksifOwnerMatch ExecCGI MultiViews
    #
    # Note that "MultiViews" must be named *explicitly* --- "Options All"
    # doesn't give it to you.
    #
    # The Options directive is both complicated and important.  Please see
    # http://httpd.apache.org/docs/2.4/mod/core.html#options
    # for more information.
    #
    Options Indexes FollowSymLinks

    #
    # AllowOverride controls what directives may be placed in .htaccess files.
    # It can be "All", "None", or any combination of the keywords:
    #   Options FileInfo AuthConfig Limit
    #
    AllowOverride FileInfo

    #
    # Controls who can get stuff from this server.
    #
    Require all granted
</Directory>
```
```
gzip.conf
<IfModule deflate_module>
SetOutputFilter DEFLATE
SetEnvIfNoCase Request_URI .(?:gif|jpe?g|png)$ no-gzip dont-vary
SetEnvIfNoCase Request_URI .(?:exe|t?gz|zip|bz2|sit|rar)$ no-gzip dont-vary
SetEnvIfNoCase Request_URI .(?:pdf|doc|avi|mov|mp3|rm)$ no-gzip dont-vary
AddOutputFilterByType DEFLATE text/html text/plain text/xml text/css
AddOutputFilterByType DEFLATE application/x-javascript
</IfModule>
```
```
prefork.conf
<IfModule prefork.c>
ServerLimit  10000
StartServers 5 
MinSpareServers 5 
MaxSpareServers 10 
MaxClients 10000 
MaxRequestsPerChild 0 
</IfModule>
```
定时脚本（不要用root账户运行这些脚本）
-----
```
~/website/oa/bin/sync.sh  定时同步考勤数据
crontab -e
30 8 * * * /home/gosling/websites/oa/bin/yesterday.sh
* * * * * /home/gosling/websites/oa/bin/sync.sh

手动同步指定日期考勤数据
php ~/website/oa/bin/sync.php -d2018-11-11

```

开源OA系统
-----

基于ranzi开源OA系统改造

* 集成域账户登陆
* 集成考勤系统
* OA 考勤管理
* 公告系统
* 员工信息查询
* 项目管理
* 报销流程管理
