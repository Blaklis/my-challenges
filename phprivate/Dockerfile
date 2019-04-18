FROM ubuntu:latest

ENV DEBIAN_FRONTEND=noninteractive
RUN apt-get -y update
RUN apt-get -y install apache2 apt-transport-https curl php php-mysqli libapache2-mod-php vim-common unzip mysql-server
ADD db.sql /
RUN find /var/lib/mysql -type f -exec touch {} \;; service mysql restart; mysql -u root -e "CREATE DATABASE phprivate;"; mysql -u root phprivate < /db.sql; mysql -u root -e "GRANT ALL PRIVILEGES ON phprivate.* TO 'web'@'localhost' IDENTIFIED BY 'web';FLUSH PRIVILEGES;"
ADD webroot /var/www/html/
RUN mkdir /var/www/files; chown -R 33:33 /var/www/files; echo "INS{YOUWON}" > /var/www/files/fl4gl0ln0p3
RUN rm /var/www/html/index.html
RUN chown -R root:root /var/www/html/
RUN chmod -r /tmp

RUN sed -i '/^disable_functions/ s/$/exec,passthru,shell_exec,system,proc_open,popen,pcntl_exec,posix_mkfifo, pg_lo_import, dbmopen, dbase_open, popen, chgrp, chown, chmod, symlink,apache_setenv,define_syslog_variables, posix_getpwuid, posix_kill, posix_mkfifo, posix_setpgid, posix_setsid, posix_uname, proc_close, pclose, proc_nice, proc_terminate,curl_exec,curl_multi_exec,parse_ini_file,show_source,imap_open,imagecolormatch,fopen,copy,rename,readfile,readlink,tmpfile,tempnam,touch,link,file,ftp_connect,ftp_ssl_connect,/' /etc/php/7.2/apache2/php.ini
RUN sed -i '/^disable_classes/ s/$/SplFileObject,SplFileInfo,SplTempFileObject,SessionHandler/' /etc/php/7.2/apache2/php.ini
RUN sed -i 's/^post_max_size = 8M/post_max_size = 30M/g' /etc/php/7.2/apache2/php.ini
RUN sed -i 's/^upload_max_filesize = 2M/upload_max_filesize = 30M/g' /etc/php/7.2/apache2/php.ini

# disable /server-status
RUN a2dismod status

CMD find /var/lib/mysql -type f -exec touch {} \;;service apache2 start;service mysql restart;  /bin/bash
