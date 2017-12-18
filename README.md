# DBdisplay
Create a markdown file of MySQL or Postgresql database schema  

## Usage
run 
```
php create_schema.php -uusername -ppassword -s127.0.0.1 -ddatabase 
```
A file will be created or overwritten using filname option or db.md by default

### options
* -u username
* -p password
* -s ip address
* -d database name 
* --driver="pgsql" -- default is mysql
* --filename="file.md" -- default db.md

Requires pdo-pgsql for postgres 