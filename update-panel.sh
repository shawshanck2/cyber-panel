
githubRepoLink="https://api.github.com/repos/mahmoud-ap/cyber-panel/releases/latest"
repoLink=$(sudo curl -Ls "${githubRepoLink}" | grep '"browser_download_url":' | sed -E 's/.*"([^"]+)".*/\1/')

sudo wget -O /var/www/html/update.zip $repoLink
sudo unzip -o /var/www/html/update.zip -d /var/www/html/panel/
sudo chown -R www-data:www-data /var/www/html/panel
chown www-data:www-data /var/www/html/panel/index.php
sudo service apache2 restart