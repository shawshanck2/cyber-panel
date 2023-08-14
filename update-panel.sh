
# Set URLs and file paths
repoLink="https://github.com/mahmoud-ap/cyber-panel/raw/master/AdminPanel/1.zip"

originalEnvFile="/var/www/html/panel/.env"
panelDir="/var/www/html/panel"

# Backup original .env file contents to a variable
originalEnvContent=$(cat "$originalEnvFile")

# Download PHP code zip file
sudo wget -O /var/www/html/update.zip $repoLink

# # Extract PHP code
unzip -o  /var/www/html/update.zip -d "$panelDir"

# # Restore original .env file contents
# echo "$original_env_content" > "$original_env_file"

# # Clean up temporary files
# rm /var/www/html/panel/tmp/php_code.zip

# echo "PHP code updated and .env content restored."
