# Introduction

These are database driven Services developed for Passive monitoring of Calls and SMS trends and FUPs.

# Configurations for Nagios Add-on

## Python Packages
Install following packages for python
```
# apt-get install python-pip
# pip install MySQL-python flask flask_restful iniparse urllib2 json sys
```

## Enable rewrite module in Apache

To enable it the rewrite module, run "apache2 enable module rewrite":
```
# sudo a2enmod rewrite
```
You need to restart the webserver to apply the changes:
```
# sudo service apache2 restart
```
If you plan on using mod_rewrite in .htaccess files, you also need to enable the use of .htaccess files by changing AllowOverride None to AllowOverride FileInfo. For the default website, edit /etc/apache2/sites-available/default:
```
    <Directory /var/www/>
            Options Indexes FollowSymLinks MultiViews
            # changed from None to FileInfo
            AllowOverride FileInfo
            Order allow,deny
            allow from all
    </Directory>
```

After such a change, you need to restart Apache again.

## Configure Database
Create database and insert data from database.sql file:
```
# mysql -h localhost -u root -p<ROOT-PASSWORD> -e "CREATE DATABASE `monitoring_panels`;"
# mysql -h localhost -u root -p<ROOT-PASSWORD> -e "CREATE USER 'monitoringuser'@'%' IDENTIFIED BY '<password>';"
# mysql -h localhost -u root -p<ROOT-PASSWORD> -e "GRANT ALL PRIVILEGES ON `monitoring_panels`.* TO 'monitoringuser'@'%';"
# mysql -h localhost -u root -p<ROOT-PASSWORD> -e "FLUSH PRIVILEGES;"
# mysql -h localhost -u root -p<ROOT-PASSWORD> monitoring_panels < database.sql

```
After creating database, add the following:
* add database credentials in monitoring_panels/include/modules/config.php and monitoring_scripts/config.ini files
* add base url of the panel in $site variable of monitoring_panels/include/modules/config.php i.e. http://localhost/monitoring_panels
* add hostname or IP Address of the server (on which monitoring_panel application is hosted) in monitoring_scripts/check_status.py and monitoring_panels/include/modules/config.php

Please note that: 
* monitoring_panels/check_status.py is the Nagios Plugin
* default username/password for Web interface is admin.

# Service Description
## Enable Python Web Service
```
# python monitoring_scripts/webservice.py &
```
Websrvice will run on default port 8888 but it can be changed from below code in monitoring_scripts/webservice.py
```
app.run(host="<hostname>", port=<port>, debug=False)
```
## Service Parameter

**API Request:** http://X.X.X.X:8888/monitor?query=acd-last-hour

Where query is the title or unique (string) id of the service to be called

## Service Responses

There are 5 types of statuses received in service response:

* Information

* Alert

* Warning

* Critical

* Error

### Information 
```
 {
  "status": "Information", 
  "title": "acd-last-hour", 
  "unit": "xyz", 
  "value": "XX.XX" OR "DATA-NOT-AVAILABLE", 
  "vendor": "XYZ"
 }
```
### Alert 
```
 {
  "status": "Alert", 
  "title": "acd-last-hour", 
  "unit": "xyz", 
  "value": "XX.XX", 
  "vendor": "XYZ"
 }
```
### Warning 
```
 {
  "status": "Warning", 
  "title": "acd-last-hour", 
  "unit": "xyz", 
  "value": "XX.XX", 
  "vendor": "XYZ"
 }
```
### Critical 
``` 
 {
  "status": "Critical", 
  "title": "acd-last-hour", 
  "unit": "xyz", 
  "value": "XX.XX", 
  "vendor": "XYZ"
 }
```
### Error
``` 
 {
  "status": "Error", 
  "value": " This is error due to <abc> reason", 
  }
```
**Note:** Error is for debugging purposes.

## How to Add New Service?

As it is database driven then following are the columns that has to be filled:

* **Database ID:** It is ID of database from which the query will be executed i.e. 1 for Vopium Slave etc.
* **Title: ** It is self-explanatory title and service param value. It should be in small letters and without space i.e. asr-last-hour etc.
* **Vendor:** It is name of partner for which the service is providing response i.e. Vopium etc.
* **Query:** It is (single result) MySQL query used for getting the response. It should be single-line i.e. SELECT ((SUM(IF(disposition='ANSWER',1,0))/COUNT(id))*100) AS 'ASR' from cdrs where created_datetime between (NOW() - INTERVAL 60 minute) AND NOW();
* **Unit:** It is the unit of the result obtained from MySQL query i.e %, minutes etc. 
* **Alert:** The value above which is Information/normal status and below is alert.  
* **Warning:** The value above which is Alert and below is Warning
* **Critical:** The value above which is Warning and below is Critical

## How to Set Status/levels of Service? 

You have to only set the success level i.e. for ASR

### Example 
**Alert:** 60 

**Warning:** 55 

**Critical:** 50 

**Note:** Alert > Warning > Critical OR Alert < Warning < Critical
 
```
 
if value <= Critical:
Critical

else if value > critical and value <= warning:
Warning
         
else if value > warning and value <= alert:
Alert

else:
Information

```

## Nagios Plugin 

Below is the usage for deploying code in nagios and same code can be used for every service with different -i value.

```
  
 $ ./check_status.py -i acd-last-hour  
 Alert: acd-last-hour is 5.5167 minutes

```
