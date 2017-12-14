# DBdisplay
Create a markdown file of MySQL or Postgresql database schema  

## Usage
run 
```
php create_schema.php -uusername -ppassword -s127.0.0.1 -ddatabase 
```
A file will be created called db.md.

### options
* -u username
* -p password
* -s ip address
* -d database name 
* --driver="pgsql" -- default is mysql