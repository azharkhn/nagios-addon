# Introduction

These are database driven Services developed for Passive monitoring of Calls and SMS trends and FUPs.

# Service Parameter

**API Request:** http://127.0.0.1:8888/monitor?query=acd-last-hour

## query 
This is the title or unique (string) id of the service to be called

# Monitoring Service Responses

There are 5 types of statuses received in service response:

* Information

* Alert

* Warning

* Critical

* Error

## Information 
```
 {
  "status": "Information", 
  "title": "acd-last-hour", 
  "unit": "xyz", 
  "value": "XX.XX" OR "DATA-NOT-AVAILABLE", 
  "vendor": "XYZ"
 }
```
## Alert 
```
 {
  "status": "Alert", 
  "title": "acd-last-hour", 
  "unit": "xyz", 
  "value": "XX.XX", 
  "vendor": "XYZ"
 }
```
## Warning 
```
 {
  "status": "Warning", 
  "title": "acd-last-hour", 
  "unit": "xyz", 
  "value": "XX.XX", 
  "vendor": "XYZ"
 }
```
## Critical 
``` 
 {
  "status": "Critical", 
  "title": "acd-last-hour", 
  "unit": "xyz", 
  "value": "XX.XX", 
  "vendor": "XYZ"
 }
```
## Error
``` 
 {
  "status": "Error", 
  "value": " This is error due to <abc> reason", 
  }
```
**Note:** Error is for debugging purposes.

# How to Add New Service?

As it is database driven then following are the columns that has to be filled:

* **Database ID:** It is ID of database from which the query will be executed i.e. 1 for Vopium Slave etc.
* **Title: ** It is self-explanatory title and service param value. It should be in small letters and without space i.e. asr-last-hour etc.
* **Vendor:** It is name of partner for which the service is providing response i.e. Vopium etc.
* **Query:** It is (single result) MySQL query used for getting the response. It should be single-line i.e. SELECT ((SUM(IF(disposition='ANSWER',1,0))/COUNT(id))*100) AS 'ASR' from cdrs where created_datetime between (NOW() - INTERVAL 60 minute) AND NOW();
* **Unit:** It is the unit of the result obtained from MySQL query i.e %, minutes etc. 
* **Alert:** The value above which is Information/normal status and below is alert.  
* **Warning:** The value above which is Alert and below is Warning
* **Critical:** The value above which is Warning and below is Critical

# How to Set Status/levels of Service? 

You have to only set the success level i.e. for ASR

## Example 
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

else:<br/>
Information

```

# Scripts for Nagios 



## Database Driven Services

Below is the usage for deploying code in nagios and same code can be used for every service with different -i value.

```
  
 $ python script.py -i acd-last-hour  
 Alert: acd-last-hour is 5.5167 minutes

```


### Python Script 
```
#!/usr/bin/python

import urllib2, json, sys

usage = "usage: script.py [-url] <ip-address-of-server>\n Note: value shouldn't be empty "
arguments = sys.argv

if '-url' in arguments and len(arguments) == 3:
    
    if arguments.index('-url') == 1:
        url = arguments[arguments.index('-url')+1]
        
        try:
            connection = urllib2.urlopen(url)
            
            if connection.getcode() == 200:
                alert = json.loads(connection.read())
                connection.close()
                
                if alert['status'] == "OK":
                    print alert['status']+": "+alert['message']
                    sys.exit(0)
                    
                elif alert['status'] == "Critical":
                    print alert['status']+": "+alert['message']
                    sys.exit(2)
                    
                else:
                    print alert['status']+": "+alert['message']
                    sys.exit(3)
            
        except urllib2.HTTPError, e:
            print "HTTP Service Error: "+str(e.getcode())
            sys.exit(3)
    
    else:
        print usage
        sys.exit(3)

else:
    print usage
    sys.exit(3)
```


