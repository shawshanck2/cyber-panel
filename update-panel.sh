
# Set URLs and file paths
php_code_url="https://github.com/mahmoud-ap/cyber-panel/raw/master/AdminPanel/1.zip"

original_env_file="/var/www/html/panel/.env"
php_code_dir="/var/www/html/panel"

# Backup original .env file contents to a variable
original_env_content=$(cat "$original_env_file")

# Download PHP code zip file
curl -L -o /var/www/html/panel/tmp/new-update.zip "$php_code_url"

# # Extract PHP code
# unzip -o /var/www/html/panel/tmp/new-update.zip -d "$php_code_dir"

# # Restore original .env file contents
# echo "$original_env_content" > "$original_env_file"

# # Clean up temporary files
# rm /var/www/html/panel/tmp/php_code.zip

# echo "PHP code updated and .env content restored."
