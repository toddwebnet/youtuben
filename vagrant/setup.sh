if [ "$EUID" -ne 0 ];  then
  echo "Please run as root"
  exit
fi

if ! grep -q "youtuben.local.com" /etc/hosts; then
  echo "append to host file"
  echo "" >> /etc/hosts
  echo "127.0.0.1 youtuben.local.com" >> /etc/hosts
fi


if [ ! -f /etc/apache2/ssl/server.crt ]; then
  echo "Installing SSL ##########################################################"
  mkdir /etc/apache2/ssl
  openssl genrsa -des3 -passout pass:x -out /etc/apache2/ssl/server.pass.key 2048
  openssl rsa -passin pass:x -in /etc/apache2/ssl/server.pass.key -out /etc/apache2/ssl/server.key
  rm /etc/apache2/ssl/server.pass.key
  openssl req -new -key /etc/apache2/ssl/server.key -out /etc/apache2/ssl/server.csr -subj "/C=US/ST=DC/L=Texas/O=Todd/OU=IT Department/CN=*.fei.com"
  openssl x509 -req -days 365 -in /etc/apache2/ssl/server.csr -signkey /etc/apache2/ssl/server.key -out /etc/apache2/ssl/server.crt

fi

cp /home/vagrant/www/youtuben/vagrant/youtuben.conf /etc/apache2/sites-available/youtuben.conf

rm -f /etc/apache2/sites-enabled/youtuben.conf

ln -s /etc/apache2/sites-available/youtuben.conf /etc/apache2/sites-enabled/youtuben.conf


mysql -uroot -ppassword < /home/vagrant/www/youtuben/vagrant/create.sql



service apache2 restart


touch /home/vagrant/www/youtuben/storage/logs/worker.log
chmod 777 -R /home/vagrant/www/youtuben/storage/logs

service supervisor stop
if [ ! -e /etc/supervisor/conf.d/supervisor-youtuben.conf ]; then
  echo "configure supervisor ##########################################################"
  cp /home/vagrant/www/youtuben/vagrant/supervisor.conf /etc/supervisor/conf.d/supervisor-youtuben.conf
  supervisorctl reread
  supervisorctl update
  supervisorctl start supervisor-youtuben:*
fi
sudo service supervisor stop
