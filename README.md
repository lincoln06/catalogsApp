# catalogsApp

### Technical catalogs browser 

## Requirements
#### For the best experience use Linux

1. PHP 8.3 
```sudo add-apt-repository ppa:ondrej/php``` and then ```sudo apt install php8.3``` 
2. PHP 8.3 MySQL ```sudo apt install php8.3-mysql```
3. PHP 8.3 curl ```sudo apt install curl``` & ```sudo apt install php8.3-curl```
4. PDO ```sudo apt install php8.3-pdo```
5. MariaDB 10.6.12 ```sudo apt install mariadb-server```
6. Composer ```https://getcomposer.org/```
7. Symfony 6.2 CLI ```curl -sS https://get.symfony.com/cli/installer | bash```
7. All the php extensions i.e. curl and pdo should be turned on in php.ini ```sudo nano /etc/php/8.3/cli/php.ini``` and ```sudo nano /etc/php/8.3/mods-available/php.ini``` - find extension name and remove semicolon ```;``` before it.


## How to run it

1. Please fork this repository and then clone it. 
2. Run ```composer install```
3. Open mariadb console ```sudo mariadb```
4. Create a database named catalogsApp ```create database catalogsApp;``` and then use it ```use catalogsApp```.
5. The file ```catalogsApp.sql``` contains a database dump with example data. After creating a database named ```catalogsApp```, just set the file as a source in mariadb CLI: ```source catalogsApp.sql```.
6. The sample password is ```profil1```. 
7. To fully enjoy this project, you just need to log in as ```god@cata.logs``` using password ```Iamtheone1!```.