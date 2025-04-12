[English](README_EN.md) | [中文](README.md)

# PackVault
PackVault is a PHP private package management panel that currently supports Github and Gitee, and theoretically can support all git protocol platforms.

## Features
- Support for Github and Gitee platforms
- Repository management
- License management
- User management
- Private package build tasks

## Configuration
### Basic Configuration
```dotenv
# Private package storage domain
PACKVAULT_DOMAIN=
# Whether to use broadcasting, disabled by default. If using broadcast, Reverb needs to be configured (Reverb is automatically installed with the project)
PACKVAULT_USE_BROADCAST=
```

### Reverb Configuration
```dotenv
REVERB_APP_ID=
REVERB_APP_KEY=
REVERB_APP_SECRET=
REVERB_HOST="127.0.0.1"
REVERB_PORT=8001
REVERB_SCHEME=http
REVERB_SERVER_PATH=
```

### Git Platform Configuration
```dotenv
# Github Configuration
GITHUB_CLIENT_ID=
GITHUB_CLIENT_SECRET=
GITHUB_CALLBACK=

# Gitee Configuration
GITEE_CLIENT_ID=
GITEE_CLIENT_SECERT=
GITEE_CALLBACK=

# Gitea Configuration (Reserved)
GITEA_CLIENT_ID==
GITEA_CLIENT_SECERT=
# Gitea domain, since Gitea is usually self-hosted, just configure the platform domain here
GITEA_INSTANCE_URI=
```

## How to Use

```shell
composer install

npm install

php artisan migrate

php artisan db:seed

php artisan key:generate

# Start local development command
composer run dev

# Start local queue with the following command
php artisan queue:listen --timeout=3000

# If Reverb is configured, start with the following command
php artisan reverb:start --host="127.0.0.1" --port="8001" --debug
```

## Default Login User
- Email: admin@packvault.com
- Password: packvault

> Remember to change the default login user

## Queue Configuration
[Managing Queues with Supervisor](https://laravel.com/docs/queues#supervisor-configuration)

## Reverb Configuration
[Configuring Reverb in Production](https://laravel.com/docs/reverb#running-reverb-in-production)

## Configuring Scheduled Tasks
Due to Gitee's limitations, you need to periodically refresh the Gitee Access token
```shell
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1
```

## Deployment (nginx)
```nginx
server
{
    listen  443  ssl http2;
    server_name your-domain;
    index index.html index.php index.htm default.php default.htm default.html;
    root /your-project/public;
    
    ssl_certificate     your-certificate.pem;  # path to pem file
    ssl_certificate_key your-certificate.key;  # path to key file
    
    # SSL validation configuration
    ssl_session_timeout  5m;    # cache validity period
    ssl_ciphers ECDHE-RSA-AES128-GCM-SHA256:ECDHE:ECDH:AES:HIGH:!NULL:!aNULL:!MD5:!ADH:!RC4:!DH:!DHE;
    ssl_protocols TLSv1 TLSv1.1 TLSv1.2 TLSv1.3;
    ssl_prefer_server_ciphers on;

    # Configure based on actual access URL, this is just an example
    location ~ ^/packvault {
        # config('packvault.path') directory for package access
        alias /your-project/storage/packvault;
        index index.html index.php index.htm default.php default.htm default.html;
        try_files $uri $uri/index.html =404;
    }

    # Reverb configuration
    location ~ ^/apps? {
        proxy_http_version 1.1;
        proxy_set_header Host $http_host;
        proxy_set_header Scheme $scheme;
        proxy_set_header SERVER_PORT $server_port;
        proxy_set_header REMOTE_ADDR $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection "Upgrade";

        # Forward to reverb configured port
        proxy_pass reverb:port;
    }

    location / {
       try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP support
    location ~ \.php$ {
        try_files $uri /index.php =404;
        fastcgi_split_path_info ^(.+\.php)(/.+)$;
        fastcgi_pass 127.0.0.1:9000;
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }	

    # Optional: Prevent access to sensitive files
    location ~* \.(env|log|git) {
        deny all;
        return 404;
    }

    access_log  /var/log/nginx/xxxx_access.log;
    error_log  /var/log/nginx/xxxx_error.log;
}
```

## Composer Configuration
```json
{
    "repositories": [
        {
            "type": "composer",
            "url": "your-domain/packvault", // Modify this according to your nginx configuration
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
