# distrib-session-mgmt

**Description**
This is a demo utilizing PHP with ElastiCache for Memcached as a distributed session management solution. 
## Instructions
**Step 1**
Create EC2 instances and use the following script to install the necessary packages:

#!/bin/bash
yum update -y 
yum -y install php httpd php-pecl-memcache
systemctl enable httpd
systemctl start httpd

Then run the following commands to ensure proper permissions are set to allow ec2-user access:

```
sudo usermod -a -G apache ec2-user
sudo chown -R ec2-user:apache /var/www
sudo chmod 2775 /var/www && find /var/www -type d -exec sudo chmod 2775 {} \;
find /var/www -type f -exec sudo chmod 0664 {} \;
```

**Step 2**
Login to the EC2 instances and make the following changes to /etc/httpd/conf.d/php.conf:

Change php_value session.save_handler from "files" to "memcache"

Change php_value session.save_path from "local" to "tcp://<FQDN of DNS Configuration Endpoint for your ElastiCache for Memcached cluster. Ex. "tcp://mymemcachedcluster.insflx.use2.cache.amazonaws.com:11211"

**Step 3**
While still logged into the EC2 instances, use the following configuration data for /etc/php.d/memcache.ini:

    ; ----- Enable memcache extension module
    extension=memcache.so
    
    ; ----- Options for the memcache module
    ; see http://www.php.net/manual/en/memcache.ini.php
    
    ;  Whether to transparently failover to other servers on errors
    memcache.allow_failover=1
    ;  Data will be transferred in chunks of this size
    ;memcache.chunk_size=32768
    ;  Autocompress large data
    ;memcache.compress_threshold=20000
    ;  The default TCP port number to use when connecting to the memcached server
    ;memcache.default_port=11211
    ;  Hash function {crc32, fnv}
    ;memcache.hash_function=crc32
    ;  Hash strategy {standard, consistent}
    memcache.hash_strategy=consistent
    ;  Defines how many servers to try when setting and getting data.
    ;memcache.max_failover_attempts=20
    ;  The protocol {ascii, binary} : You need a memcached >= 1.3.0 to use the binary protocol
    ;  The binary protocol results in less traffic and is more efficient
    ;memcache.protocol=ascii
    ;  Redundancy : When enabled the client sends requests to N servers in parallel
    ;memcache.redundancy=1
    memcache.session_redundancy=2
    ;  Lock Timeout
    ;memcache.lock_timeout = 15
    
    ; ----- Options to use the memcache session handler
    
    ; RPM note : save_handler and save_path are defined
    ; for mod_php, in /etc/httpd/conf.d/php.conf
    ; for php-fpm, in /etc/php-fpm.d/*conf
    
    ;  Use memcache as a session handler
    session.save_handler=memcache
    ;  Defines a comma separated of server urls to use for session storage
    session.save_path="tcp://omymemcachedcluster.insflx.use2.cache.amazonaws.com:11211persistent=1&weight=1&timeout=1&retry_interval=15"

**Step 4**
Move all *.php files into your HTML directory:

    mv *.php /var/www/html

**Step 5**
Restart Apache:

    sudo systemctl restart httpd

**Test the Web Server by visiting the public IP address (in your browser) associated with the instance**

Verify connectivity from EC2 instance(s) to the Memcached cluster using the following command:

    telnet <FQDN of Memcached Endpoint> 11211

If successful, you're finished. If not, please submit an Issue.
