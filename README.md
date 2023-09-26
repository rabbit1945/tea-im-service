xiaogongtx-Im 1.0
===============

> 运行环境要求PHP7.2+，兼容PHP8.1

## 安装
### 安装 thinkphp6 具体查看官方手册
~~~
composer create-project topthink/think tp 6.0.*
~~~

如果需要更新框架使用
~~~
composer update topthink/framework
~~~
### docker 安装php环境
一、安装docker
安装命令如下：

`curl -fsSL https://get.docker.com | bash -s docker --mirror Aliyun
`

测试docker是否安装成功：

`sudo docker help
`

二、配置镜像加速

Docker 下载镜像时，如果不配置镜像加速，下载镜像会比较慢，常见的国内镜像加速地址包括：
~~~
#腾讯云的镜像地址
https://mirror.ccs.tencentyun.com

#网易的镜像地址
http://hub-mirror.c.163.com

#阿里云的镜像地址
https://{自己的阿里云ID}.mirror.aliyuncs.com
~~~

可以通过修改daemon配置文件/etc/docker/daemon.json来使用加速器：
~~~
sudo mkdir -p /etc/docker
sudo vim /etc/docker/daemon.json
~~~

json文件输入以下内容，表示使用网易的镜像加速地址：
~~~
{
"registry-mirrors": ["http://hub-mirror.c.163.com"]
}
~~~
配置好镜像加速以后，需要重启docker服务：
~~~
sudo systemctl daemon-reload
sudo systemctl restart docker
~~~

三、从docker hub拉取镜像
~~~
docker compose up -d
~~~


四、设置端口

  查看端口
  ~~~
  netstat -aptn #查看所有开放端口
   ~~~
  查看已经开启的端口
  ~~~
  sudo ufw status
  ~~~
  打开端口
  ~~~
  sudo ufw allow 9123
  ~~~
  开启防火墙
  ~~~
  sudo ufw enable
  ~~~
  重启防火墙
  ~~~
  sudo ufw reload
  ~~~

  五、配置mysql

  创建数据库名为wedo，并指定为utf8字符集
   ~~~
  create database xxxxxxxx default character set utf8mb4 collate utf8mb4_unicode_ci;
   ~~~
  创建用户，用户名为wedo，并指定密码
   ~~~
  CREATE USER 'xxxxxxxx'@'%' IDENTIFIED BY 'xxxxxxxx';
   ~~~
  给用户添加所创建数据库的所有权限，包括远程登陆
   ~~~
  grant all privileges on xxxxxxxx.* to 'xxxxxxxx'@'%' identified by 'xxxxxxxx' with grant option;
   ~~~
  刷新
   ~~~
  flush privileges;
   ~~~
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

