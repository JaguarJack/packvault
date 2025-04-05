# PackVault
PackVault 是一款 PHP 私有包管理面板，目前支持 Github 和 Gitee，理论上可以支持所有 git 协议平台。

## 功能
- 支持平台 Github 和 Gitee
- 管理仓库
- 管理 license
- 管理用户
- 构建私有包任务

## 配置
### 基础配置
```dotenv
#私有包存储域名
PACKVAULT_DOMAIN=
#是否使用广播，默认不使用。如果使用广播，需要配置 reverb，项目自动安装了 reverb
PACKVAULT_USE_BROADCAST=
```

### Reverb 配置
```dotenv
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST="127.0.0.1"
REVERB_PORT=8001
REVERB_SCHEME=http
REVERB_SERVER_PATH=
```

### Git平台配置
```dotenv
#Github配置
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_CALLBACK=

#Gitee配置
GITEE_CLIENT_ID=
GITEE_CLIENT_SECERT=
GITEE_CALLBACK=

# Gitea 配置（暂时保留）
GITEA_CLIENT_ID==
GITEA_CLIENT_SECERT=
# Gitea域名，Gitea 一般都是 self host，所以这里配置平台域名就可以了
GITEA_INSTANCE_URI=
```


## 如何使用

```shell
composer install

npm install

php artisan migrate

php artisan db:seed

php artisan key:generate

# 启动本地开发命令
composer run dev

# 启动本地队列，启动下面的命令
php artisan queue:listen --timeout=3000

# 如果配置好了 Reverb，本地配置启动下面的命令
php artisan reverb:start --host="127.0.0.1" --port="8001" --debug

```

## 默认登录用户
- 邮箱: admin@packvault.com
- 密码: packvault

> 记得修改默认登录用户

## 配置 Queue
[使用 Supervisor 管理队列](https://laravel-docs.catchadmin.com/docs/11/digging-deeper/queues#supervisor-%E9%85%8D%E7%BD%AE)

## 配置 Reverb
[生产环境配置 Reverb](https://laravel-docs.catchadmin.com/docs/11/packages/reverb#%E5%9C%A8%E7%94%9F%E4%BA%A7%E7%8E%AF%E5%A2%83%E4%B8%AD%E8%BF%90%E8%A1%8C-reverb)

## 配置定时任务
由于 Gitee 的限制，需要定时重新获取 Gitee 的 Access token，所以需要重新获取
```shell
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```
## 部署（nginx）
```
server
{
    listen  443  ssl http2;
    server_name 你的域名;
    index index.html index.php index.htm default.php default.htm default.html;
    root /你的项目/public;
    
    ssl_certificate     你的证书 pem;  # pem文件的路径
    ssl_certificate_key  你的证书 key; # key文件的路径
    
    # ssl验证相关配置
    ssl_session_timeout  5m;    #缓存有效期
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE:ECDH:AES:HIGH:!NULL:!aNULL:!MD5:!ADH:!RC4:!DH:!DHE;
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;

    # 这里根据实际访问 URL 配置，这里只是一个示例
    location ~ ^/packvault {
        # config('packvault.path') 目录，用来访问包的
        alias /你的项目/storage/packvault;
        index index.html index.php index.htm default.php default.htm default.html;
        try_files $uri $uri/index.html =404;
    }

    # reverb 配置
    location ~ ^/apps? {
        proxy_http_version 1.1;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header SERVER_PORT $server_port;
        proxy_set_header REMOTE_ADDR $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";

        # 转发到 reverb 配置的端口
        proxy_pass reverb:端口;
    }

    location / {
       try_files $uri $uri/ /index.php?$query_string;
    }

   # PHP 支持
    location ~ \.php$ {
        try_files $uri /index.php =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }	

    # 可选：防止访问敏感文件
    location ~* \.(env|log|git) {
        deny all;
        return 404;
    }

    access_log  /var/log/nginx/xxxx_access.log;
    error_log  /var/log/nginx/xxxx_error.log;
}
```

## composer 配置
```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "你的域名/packvault", // 这个根据 nginx 配置实际修改
            "only-dist": true,
            "options": {
                "ssl": {
                    "verify_peer": false,
                    "verify_peer_name": false
                }
            }
        }
    ]
}
```
