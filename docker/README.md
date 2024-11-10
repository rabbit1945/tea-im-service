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

## 3、下载CRMEB程序

建议去下载最新开源代码 https://gitee.com/gongzhiyang/lightning-service.git
程序放到docker-compose 同级目录下

## 4、启动项目

```
进入docker-compose目录 cd /.docker

运行命令：docker-compose up -d

## 5、访问 系统
http://localhost:8078/
## 6、安装CRMEB
### Mysql数据库信息：
```

Host:192.168.1.10
Post:3306
user:root
pwd:lightningAbc123qwe

```
### Redis信息：
```

Host:192.168.10.11
Post:6379
db:0
pwd:123456

```
## 7、常见问题
1、端口被占用进入docker-compose.yml 里面修改端口

2、如果运行docker-compose up -d 启动失败，请查看docker-compose.yml 修改里面镜像地址或其它配置


