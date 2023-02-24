```shell
cd docker
sudo docker-compose up -d
```

```shell
# 生成随机密码
MYSQL_PASSWORD=$(</dev/urandom tr -dc 'A-Za-z0-9' | head -c 12  ; echo)
# 输出随机密码
echo $MYSQL_PASSWORD
# 初始化MySQL
sudo docker run --name mysql \
        -e MYSQL_ROOT_PASSWORD="$MYSQL_PASSWORD"\
        -p 3306:3306 \
        -v /data/mysql/data:/var/lib/mysql:rw \
        -d mysql:5.6.29
# 等待MySQL启动完成
# 出现 Version: '5.6.29'  socket: '/var/run/mysqld/mysqld.sock'  port: 3306
sudo docker logs -f --tail 1000 mysql
# 初始化完成后清理MySQL
sudo docker stop mysql ; sudo docker rm mysql
# 重新创建正常运行的服务
sudo docker run --name mysql \
        --restart=always -p 3306:3306 \
        -v /data/mysql/data:/var/lib/mysql:rw \
        --add-host docker-master:$DOCKER_MASTER \
        -d mysql:5.6.29
# 进入mysql容器
sudo docker exec -it mysql bash
# 登录mysql，输入密码 (上面随机生成的密码)
mysql -u root -p
# 创建数据库xxx
create database xxx;
# 退出mysql
exit
# 退出容器
exit
```

```shell
sudo docker run --privileged \
        --name php56 --restart=always \
        --add-host docker-master:$DOCKER_MASTER \
        -v /data/website:/data/website:rw \
        -v /data/sock:/sock:rw \
        -d modstart/php56_sock:latest

sudo docker run --name tengine -p 80:80 -p 443:443 --restart=always \
        --add-host docker-master:$DOCKER_MASTER \
        -v /data/website:/data/website:rw \
        -v /data/sock:/data/sock:rw \
        -v /data/nginx/conf.d:/etc/nginx/conf.d:ro \
        -d modstart/tengine:latest

http://xxx.com/install.php

sudo docker run --privileged \
        --name supervisor_php56 --restart=always \
        --add-host docker-master:$DOCKER_MASTER \
        -v /data/supervisord.d:/etc/supervisord.d:rw \
        -v /data/cron.d:/etc/cron.d:rw \
        -v /data/website:/data/website:rw \
        -d modstart/supervisor_php56:latest
```