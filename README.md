lightning-server 1.0
===============


> 运行环境要求PHP8.0.3 swoole 4.0  topthink/think-swoole 3.0  swoole 5.1.0
> 
> 手册
> https://note.youdao.com/s/RGpaOAas

# 安装
## 安装 thinkphp6 具体查看官方手册
~~~        
composer create-project topthink/think tp 6.0.*
~~~

如果需要更新框架使用
~~~
composer update topthink/framework
~~~

# docker-compose 快速运行项目
## 1、安装docker
docker 官网下载
https://www.docker.com/products/docker-desktop

或命令安装
```
curl -sSL https://get.daocloud.io/docker | sh
```
## 2、安装docker-compose
https://www.runoob.com/docker/docker-compose.html
## 3、下载lightning程序
建议去下载最新开源代码 https://gitee.com/gongzhiyang/lightning-service.git
程序放到docker-compose 同级目录下
## 4、启动项目
```
进入docker-compose目录 cd /.docker

运行命令：docker-compose up -d

## 5、访问 系统
http://localhost:8078/
```
```
### Mysql数据库信息：
表结构文件：[webim.sql](.docker%2Fmysql%2Fwebim.sql)
Host:192.168.1.10
Post:3306
user:root
pwd:lightningAbc123qwe

```

```
### Redis信息：
Host:192.168.10.11
Post:6379
db:0
pwd:123456
```


```

## 常见问题
1、端口被占用进入docker-compose.yml 里面修改端口

2、如果运行docker-compose up -d 启动失败，请查看docker-compose.yml 修改里面镜像地址或其它配置

```

## 配置

### nginx 配置 
 ~~~
 
server {
    listen 80;
    index index.php index.html;
    server_name lightning.gzy;

    root /home/www/lightning-service/public;


    location / {
        if (!-e $request_filename) {
           rewrite  ^(.*)$  /index.php?s=$1  last; break;
        }
    }

    location ~ .*\.(gif|jpg|jpeg|png|bmp|swf)$
    {
        expires      30d;
        error_log off;
        access_log /dev/null;
    }

    location ~ .*\.(js|css)?$
    {
        expires      12h;
        error_log off;
        access_log /dev/null;
    }
   location ~ [^/]\.php(/|$)
   	{
   		try_files $uri =404;

   		fastcgi_pass  php-fpm:9000;

   		fastcgi_index index.php;
   		fastcgi_param  SCRIPT_FILENAME    $document_root$fastcgi_script_name;
        fastcgi_param  QUERY_STRING       $query_string;
        fastcgi_param  REQUEST_METHOD     $request_method;
        fastcgi_param  CONTENT_TYPE       $content_type;
        fastcgi_param  CONTENT_LENGTH     $content_length;

        fastcgi_param  SCRIPT_NAME        $fastcgi_script_name;
        fastcgi_param  REQUEST_URI        $request_uri;
        fastcgi_param  DOCUMENT_URI       $document_uri;
        fastcgi_param  DOCUMENT_ROOT      $document_root;
        fastcgi_param  SERVER_PROTOCOL    $server_protocol;
        fastcgi_param  REQUEST_SCHEME     $scheme;
        fastcgi_param  HTTPS              $https if_not_empty;

        fastcgi_param  GATEWAY_INTERFACE  CGI/1.1;
        fastcgi_param  SERVER_SOFTWARE    nginx/$nginx_version;

        fastcgi_param  REMOTE_ADDR        $remote_addr;
        fastcgi_param  REMOTE_PORT        $remote_port;
        fastcgi_param  SERVER_ADDR        $server_addr;
        fastcgi_param  SERVER_PORT        $server_port;
        fastcgi_param  SERVER_NAME        $server_name;
        set $real_script_name $fastcgi_script_name;
        if ($fastcgi_script_name ~ "^(.+?\.php)(/.+)$") {
        set $real_script_name $1;
        set $path_info $2;
         }


   	}  
}
 ~~~

## swoole 

 ~~~
 启动HTTP服务（默认）

php think swoole start
停止服务

php think swoole stop
重启服务

php think swoole restart
reload服务

php think swoole reload
 ~~~

## think-queue 队列
 ~~~
 php think queue:listen --queue MessageJobQueue
 ~~~

## Supervisor 配置使用
### 安装
 ~~~
  apt-get install -y supervisor
 ~~~

### 主进程配置

[supervisord]
~~~
.docker/php/supervisord.conf
~~~
 ~~~
logfile=/var/log/supervisor/supervisord.log ; (main log file;default /supervisord.log)
logfile_maxbytes=50MB       ; (max main logfile bytes b4 rotation;default 50MB)
logfile_backups=10          ; (num of main logfile rotation backups;default 10)
loglevel=info               ; (logging level;default info; others: debug,warn)
pidfile=/var/run/supervisord.pid ; (supervisord pidfile;default supervisord.pid)
nodaemon=true               ; (start in foreground if true;default false)
minfds=1024                 ; (min. avail startup file descriptors;default 1024)
minprocs=200                ; (min. avail process descriptors;default 200)

[supervisorctl]
serverurl=unix:///var/tmp/supervisor.sock ; use a unix:// URL  for a unix socket
[inet_http_server]
port = 127.0.0.1:9100
username=user              ; default is no username (open server)
password=123               ; default is no password (open server)
[rpcinterface:supervisor]
supervisor.rpcinterface_factory = supervisor.rpcinterface:make_main_rpcinterface
[unix_http_server]
file = /var/tmp/supervisor.sock

[include]
files = supervisord.d/*.conf
 ~~~

### 应用程序配置
~~~
.docker/php/supervisord.d
~~~

#### php-fpm 

 ~~~
[program:php-fpm]
command = php-fpm
autostart=true
autorestart=true
startsecs=5
startretries=3                           ;启动尝试次数
stdout_logfile_maxbytes = 50MB
stdout_logfile_backups = 200
stderr_logfile=/var/log/supervisor/php-fpm-worker_err.log        ;标准输出的位置
stdout_logfile=/var/log/supervisor/php-fpm-worker_out.log        ;标准错误输出的位置
 ~~~

#### swoole 
 ~~~
 [program:tp-swoole-worker]

command=   php think swoole start
directory= /home/www/lightning-service ; 项目执行目录
autostart=true                           ;是否随supervisor启动
autorestart=true                         ;是否在挂了之后重启，意外关闭后会重启，比如kill掉！
startsecs=5
startretries=3                           ;启动尝试次数
stdout_logfile_maxbytes = 50MB
stdout_logfile_backups = 200
stderr_logfile=/var/log/supervisor/tp-swoole-worker_err.log        ;标准输出的位置
stdout_logfile=/var/log/supervisor/tp-swoole-worker_out.log        ;标准错误输出的位置
 ~~~

#### queue 
 ~~~
 [program:tp-queue-worker]
command= php think queue:listen --queue MessageJobQueue  --tries 3;
process_name=%(program_name)s_%(process_num)02d              ;多进程名称肯定不同，匹配多个
numprocs=1                                                   ;启动多个进程
directory= /home/www/lightning-service ; 项目执行目录
autostart=true                           ;是否随supervisor启动
autorestart=true                         ;是否在挂了之后重启，意外关闭后会重启，比如kill掉！
startsecs=5
startretries=3                           ;启动尝试次数
stderr_logfile=/var/log/supervisor/tp-queue-worker_err.log        ;标准输出的位置
stdout_logfile=/var/log/supervisor/tp-queue-worker_out.log        ;标准错误输出的位置

 ~~~
