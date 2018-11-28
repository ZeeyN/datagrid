# Datagrid

This work has done as a testing task

## Instailing\Starting

After finish download all files you need to:

- Get this project into your server domains directory.
- Open file db_config.php that stays in datagrid\includes.
- Enter your database system and create empty database, you'll need it later.

## Than you need to change defined variables:

- DB_HOST is your hostname.
- DB_USER is a username for get access to your database system.
- DB_PASS is a password to your database.
- DB_NAME is a name of database that you created before
- DB_TABLE_VERSIONS using in scripts. Do not change it, or you will brake migration system.
- DB_MYSQL_DIR this project working with MySQL for now, and for good link with database you need to change this variable on the directory, where stands your mysql executional file, ecraning <\\> symbols with another <\\> (Example: <C:\\\Username\\\eth.>) .
