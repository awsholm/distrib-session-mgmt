# distrib-session-mgmt

**Description**
This is a demo utilizing PHP with ElastiCache for Memcached as a distributed session management solution. 
## Instructions
**Step 1**
Create Amazon Linux x86 EC2 instances and use the following User Data (Instance Details, bottom of page) to configure the instance:

```
#!/bin/bash
yum update -y
amazon-linux-extras enable php7.3
yum clean metadata
yum -y install php
usermod -a -G apache ec2-user
chown -R ec2-user:apache /var/www
chmod 2775 /var/www && find /var/www -type d -exec sudo chmod 2775 {} \;
find /var/www -type f -exec  chmod 0664 {} \;
echo "<?php phpinfo(); ?>" > /var/www/html/phpinfo.php
systemctl enable httpd
systemctl start httpd
```

**Step 2**
Login to the EC2 instances and install the ElastiCache auto-discovery PHP client (7.3):
```
wget https://elasticache-downloads.s3.amazonaws.com/ClusterClient/PHP-7.3/latest-64bit
tar xvf latest-64bit
sudo mv amazon-elasticache-cluster-client.so /usr/lib64/php/modules/
echo "extension=amazon-elasticache-cluster-client.so" | sudo tee --append /etc/php.d/50-memcached.ini
```

Edit the /etc/php.ini file:
```
Change session.save_handler from "files" to "memcached"

Change session.save_path from "local" to "tcp://<FQDN of DNS Configuration Endpoint for your ElastiCache for Memcached cluster>". Ex. "tcp://mymemcachedcluster.insflx.use2.cache.amazonaws.com:11211"
```

**Step 3**
While still logged into the EC2 instances, use the following configuration data for /etc/php.d/50-memcache.ini (replace configuration endpoint with your own):
```
    ;  Use memcache as a session handler
    session.save_handler=memcached
    ;  Defines a comma separated of server urls to use for session storage
    session.save_path="tcp://<yourclusterendpointFQDN>:11211persistent=1&weight=1&timeout=1&retry_interval=15"
```
**Step 4**
Upload php_files.tar to your EC2 instances. Login to each instance, untar and move all *.php files into your HTML directory:

    ```
    ---From your local machine, upload php_files.tar to each instance---
    Ex. scp -i /path/to/key.pem php_files.tar ec2-user@<IP of instance>:
    
    ---connect to EC2 instance(s)---
    tar xvf php_files.tar && cd php_files
    mv *.php /var/www/html
    ```
    
**Step 5**
Restart Apache:
```
    sudo systemctl restart httpd
```
**Test the Web Server by visiting the public IP address (in your browser) associated with the instance**

Install telnet and verify connectivity from EC2 instance(s) to the Memcached cluster using the following command:

```
    sudo yum -y install telnet
    telnet <FQDN of Memcached Endpoint> 11211
```

If successful, you're finished. If not, please submit an Issue.
