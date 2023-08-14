
# Set URLs and file paths
repoLink="https://github.com/mahmoud-ap/cyber-panel/raw/master/AdminPanel/1.zip"

originalEnvFile="/var/www/html/panel/.env"
panelDir="/var/www/html/panel"

# Backup original .env file contents to a variable
originalEnvContent=$(cat "$originalEnvFile")

# Download PHP code zip file
sudo wget -O /var/www/html/update.zip $repoLink

# # Extract PHP code
sudo unzip -o /var/www/html/update.zip -d $panelDir
wait
# # Restore original .env file contents
echo "$originalEnvContent" > "$originalEnvFile"

sudo chown -R www-data:www-data /var/www/html/panel
wait
chown www-data:www-data /var/www/html/panel/index.php

echo "PHP code updated and .env content restored."
