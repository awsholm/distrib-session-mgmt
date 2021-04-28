# distrib-session-mgmt

**Description**
This is a demo utilizing PHP with ElastiCache for Memcached as a distributed session management solution. 
## Instructions
**Step 1**
Create Amazon Linux x86 EC2 instances and use the following User Data (Instance Details, bottom of page) to configure the instance:

```
#!/bin/bash
yum update -y
yum install -y gcc-c++ zlib-devel
amazon-linux-extras enable php7.4
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
Configure EC2 instances with proper security groups to permit communication with ElastiCache Memcached
Use [this link](https://docs.aws.amazon.com/AmazonElastiCache/latest/mem-ug/accessing-elasticache.html) as a reference.

**Step 3**
Login to the EC2 instances & install the ElastiCache auto-discovery PHP client (7.4):

```
wget https://elasticache-downloads.s3.amazonaws.com/ClusterClient/PHP-7.4/latest-64bit-X86
tar xvf latest-64bit
sudo mv amazon-elasticache-cluster-client.so /usr/lib64/php/modules/
```

**Step 4**
Create the file /etc/php.d/50-memcached.ini with the following:

```
session.save_handler=memcached
session.save_path="tcp://<yourclusterendpointFQDN>:11211persistent=1&weight=1&timeout=1&retry_interval=15"
```

**Step 5**
Upload php_files.tar to your EC2 instances. :

```
Ex. scp -i /path/to/key.pem php_files.tar ec2-user@<IP of instance>:
```

**Step 6**
Login to each instance, untar and move all php files into your HTML directory:

```
tar xvf php_files.tar && cd php_files
mv *.php /var/www/html
```
    
**Step 7**
Restart Apache:

```
sudo systemctl restart httpd
```

**Step 8**
Install telnet and verify connectivity from EC2 instance(s) to the Memcached cluster using the following command:

```
sudo yum -y install telnet
telnet <FQDN of Memcached Endpoint> 11211
```

**Step 9**
Test the Web Server by visiting the public IP address (in your browser) associated with the instance


If successful, you're finished! If not, please submit an [Issue](https://github.com/awsholm/distrib-session-mgmt/issues).
