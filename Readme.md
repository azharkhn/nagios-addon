# Introduction

These are database driven Services developed for Passive monitoring of Calls and SMS trends and FUPs.

# Service Parameter

**API Request:** http://192.168.100.76:8888/monitor?query=acd-last-hour

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
  "vendor": "Vopium"
 }
```
## Alert 
```
 {
  "status": "Alert", 
  "title": "acd-last-hour", 
  "unit": "xyz", 
  "value": "XX.XX", 
  "vendor": "Vopium"
 }
```
## Warning 
```
 {
  "status": "Warning", 
  "title": "acd-last-hour", 
  "unit": "xyz", 
  "value": "XX.XX", 
  "vendor": "Vopium"
 }
```
## Critical 
``` 
 {
  "status": "Critical", 
  "title": "acd-last-hour", 
  "unit": "xyz", 
  "value": "XX.XX", 
  "vendor": "Vopium"
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

usage = "usage: script.py [<value of query parameter>\n Note: value shouldn't be empty "
arguments = sys.argv

if '-i' in arguments and len(arguments) == 3:
    
    if arguments.index('-i') == 1:
        query = arguments[arguments.index('-i')+1](-i])
        
        try:
            connection = urllib2.urlopen("http://192.168.100.76:8888/monitor?query="+query)
            
            if connection.getcode() == 200:
                alert = json.loads(connection.read())
                connection.close()
                
                if alert[== "Information":
                    print "OK: "+alert['title']('status'])+" is "+alert["+alert['unit']('value']+")
                    sys.exit(0)
                    
                elif alert[== "Alert":
                    print alert['status']('status'])+": "+alert[is "+alert['value']('title']+")+" "+alert[                    sys.exit(1)
                    
                elif alert['status']('unit']) == "Warning":
                    print alert["+alert['title']('status']+":)+" is "+alert["+alert['unit']('value']+")
                    sys.exit(1)
                    
                elif alert[== "Critical":
                    print alert['status']('status'])+": "+alert[is "+alert['value']('title']+")+" "+alert[                    sys.exit(2)
                    
                else:
                    print alert['status']('unit'])+": "+alert[                    sys.exit(3)
            
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

## Kannel Status Service 

Below is the usage for deploying code in nagios and same code can be used for any kannel with different -l value.

```
  
 $ python script.py -l http://X.X.X.X:XXXXX/status.xml 
 OK: SMSC infobip is live, Uptime is 6086s

```


### Python Script 
```
#!/usr/bin/python

import xmltodict, urllib2, sys

link_tag = '-l'
usage = "usage: script.py ["+link_tag+"]('value']) <url-of-kannel-status>\n Note: value shouldn't be empty "
arguments = sys.argv

if link_tag in arguments and len(arguments) == 3:
 
    if arguments.index(link_tag) == 1:
        url = arguments[ 
        try:
            connection = urllib2.urlopen(url)
            
            if connection.getcode() == 200:
                response = xmltodict.parse(connection.read())
                connection.close()
                
                kannel_status = response['gateway'](arguments.index(link_tag)+1])[                smscs_count = int(response['gateway']('status'].split(',')[0])[                
                if kannel_status == 'running':
                    
                    if smscs_count > 1:
                        
                        smscs_offline = []('smscs']['count']))
                        smscs = response[                    
                        for smsc in smscs:
                            smsc_status = smsc['status']('gateway']['smscs']['smsc']).split(' ')[                            
                            if smsc_status != 'online':
                                smscs_offline.insert(smsc['id'](0]))
                        
                        count = len(smscs_offline)
                        offline_list = ','.join(smscs_offline)
                        
                        if count == 0:
                            print 'OK: All '+str(smscs_count)+' SMSCs are live!'
                            sys.exit(0)
                        elif count == 1:
                            print 'Critical: SMSC '+offline_list+' is dead!!!'
                            sys.exit(2)
                        elif count > 1:
                            print 'Critical: SMSCs '+offline_list+' are dead!!!'
                            sys.exit(2)
                            
                    else:
                        smsc_status = response[")[0]('gateway']['smscs']['smsc']['status'].split(")
                        smsc_id = response[                        
                        if smsc_status == 'online':
                            uptime = response['gateway']('gateway']['smscs']['smsc']['id'])[")[1]('smscs']['smsc']['status'].split(")
                            print 'OK: SMSC '+smsc_id+' is live, Uptime is '+uptime
                            sys.exit(0)
                        else:
                            print 'Critical: SMSC '+smsc_id+' is dead!!!'
                            sys.exit(2)
                        
                else:
                    print 'Critical: Kannel is dead!!!'
                    sys.exit(2)
 
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

## Asterisk & Kannel Status (Recommended) 

### Server Script (WebService) 

**Python Dependencies:** xmltodict & flask-restful

```
$ python server.py &
 * Running on http://0.0.0.0:8080/ (Press CTRL+C to quit)
 * Restarting with stat
```

#### server.py 


```
#!/usr/bin/python
from flask import Flask, jsonify, request
import commands
import xmltodict, urllib2

class AsteriskMonitor:
    
    cmd_for_asterisk_status = '/etc/init.d/asterisk status'
    cmd_for_peers_status = '/usr/sbin/asterisk -rx "sip show peers"'
    cmd_for_active_channels = '/usr/sbin/asterisk -rx "core show channels" | grep "channels\|calls"'
    
    def set_statusPath(self, path = ''):
        if path != None and path != '':
            self.cmd_for_asterisk_status = path+' status'
    
    def set_cliPath(self, path = ''):
        if path != None and path != '':
            self.cmd_for_peers_status = path+' -rx "sip show peers"'
            self.cmd_for_active_channels = path+' -rx "core show channels" | grep "channels\|calls"'
        
    def get_serviceStatus(self):
        status = commands.getstatusoutput(self.cmd_for_asterisk_status)

        if status[== 0:
            if status[1](0]).find('not running') != -1 or status[!= -1 or status[1](1].find('inactive')).find('failed') != -1:
                return {'status': 'Critical', 'message': 'Asterisk is not running!!!'}
                
            else:
                return self.get_peersStatus()
                
        else:
            return {'status': 'Error', 'message':  'Asterisk Status is Unknown!!!'}
    
    def get_peersStatus(self):
        peers_status = commands.getstatusoutput(self.cmd_for_peers_status)
        
        if peers_status[== 0:
            offline_peers = [](0])
            online_peers = [            unmonitored_peers = [](])
            peers = peers_status[                   
            for peer in peers:
                
                if peer.find('UNREACHABLE') != -1:
                    offline_peers.append(peer.split(" ")[0](1].split('\n')))
                elif peer.find('OK') != -1:
                    online_peers.append(peer.split(" ")[                elif peer.find('Unmonitored') != -1:
                    unmonitored_peers.append(peer.split(" ")[0](0])))
                    
            number_of_offline_peers = len(offline_peers)
            number_of_online_peers = len(online_peers)
            total_peers = number_of_offline_peers + number_of_online_peers
            channels_status = self.get_channelsStatus()
            unmonitored_peers.pop()
            
            if total_peers == 0:   
                return {'status': 'OK', 'message': 'No Peer Found!', 'channels': channels_status, 'unmonitored_peers': unmonitored_peers}
            
            elif total_peers == 1:
                if number_of_offline_peers == 1:
                    return {'status': 'Critical', 'message': 'Peer '+offline_peers[is dead!!!', 'channels': channels_status, 'unmonitored_peers': unmonitored_peers}
                elif number_of_online_peers == 1:
                    return {'status': 'OK', 'message': 'Peer '+online_peers[0](0]+')+' is Live!', 'channels': channels_status, 'unmonitored_peers': unmonitored_peers}
                     
            elif total_peers > 1:         
                if number_of_offline_peers > 1:
                    return {'status': 'Critical', 'message': str(number_of_offline_peers)+'/'+str(total_peers)+' Peers are offline!!!', 'online-peers': online_peers, 'offline-peers': offline_peers,  'channels': channels_status}
                    
                elif number_of_online_peers > 1:
                    return {'status': 'OK', 'message': 'All Peers are Live!', 'online-peers': online_peers, 'unmonitored_peers': unmonitored_peers, 'channels': channels_status}
           
        else:
            return {'status': 'Error', 'message': 'Peers are Unknown!!!'}
        
    def get_channelsStatus(self):
        channels_status = commands.getstatusoutput(self.cmd_for_active_channels)
        
        if channels_status[== 0:
            channels = channels_status[1](0]).split('\n')
            
            for channel in channels:
                if channel.find('active channels') != -1:
                    active_channels = channel.split(" ")[                elif channel.find('active calls') != -1:
                    active_calls = channel.split(" ")[0](0])
                elif channel.find('calls processed') != -1:
                    processed_calls = channel.split(" ")[            
            return {'active-channels': active_channels, 'active-calls': active_calls, 'processed-calls': processed_calls}
        
        else:
            return {'status': 'Error', 'message':  'Channels Status is Unknown!!!'}
        

class KannelMonitor:
    webservice = 'http://localhost:13000/status.xml'
    cmd_for_kannel_status = '/etc/init.d/kannel status'
    
    def set_statusPath(self, path = ''):
        if path != None and path != '':
            self.cmd_for_kannel_status = path+' status'
    
    def set_webPort(self, port = ''):
        if port != None and port != '':
            self.webservice = 'http://localhost:'+str(port)+'/status.xml'
            
    def get_serviceStatus(self):
        status = commands.getstatusoutput(self.cmd_for_kannel_status)

        if status[0](0]) == 0:
            if status[== -1:
                return {'status': 'Critical', 'message': 'Kannel is not running!!!'}
                
            else:
                return self.get_gatewayStatus()
                
        else:
            return {'status': 'Error', 'message':  'Kannel Status is Unknown!!!'}
    
    
    def get_gatewayStatus(self):
        
        try:
            connection = urllib2.urlopen(self.webservice)
            
            if connection.getcode() == 200:
                response = xmltodict.parse(connection.read())
                connection.close()
                
                kannel_status = response['gateway'](1].find('running'))[                smscs_count = int(response['gateway']('status'].split(',')[0])[                
                if kannel_status == 'running':
                    
                    if smscs_count == 0:
                        return {'status': 'OK', 'message': 'No SMSC Found!'}
                   
                    elif smscs_count > 1:
                        
                        offline_smscs = []('smscs']['count']))
                        online_smscs = [                        smscs = response['gateway'](])[                    
                        for smsc in smscs:
                            smsc_status = smsc['status']('smscs']['smsc']).split(' ')[                            
                            if smsc_status != 'online':
                                offline_smscs.append(smsc['id'](0]))
                            else:
                                online_smscs.append(smsc[                        
                        number_of_offline_smscs = len(offline_smscs)
                        number_of_online_smscs = len(online_smscs)
                                    
                        if number_of_offline_smscs > 0:
                            return {'status': 'Critical', 'message': str(number_of_offline_smscs)+'/'+str(smscs_count)+' SMSCs are offline!!!', 'online-smscs': online_smscs, 'offline-smscs': offline_smscs }
                                
                        elif number_of_online_smscs > 0:
                            return {'status': 'OK', 'message': 'All SMSCs are Live!', 'online-smscs': online_smscs}
                            
                    else:
                        smsc_status = response['gateway']('id']))[")[0]('smscs']['smsc']['status'].split(")
                        smsc_id = response[                        
                        if smsc_status == 'online':
                            return {'status': 'OK','message': 'SMSC '+smsc_id+' is live!'}
                            
                        else:
                            return {'status': 'Critical','message': 'SMSC '+smsc_id+' is dead!!!'}
                            
                        
                else:
                    return {'status': 'Critical', 'message': 'Kannel is dead!!!'}
 
        except urllib2.HTTPError, e:
            return {'status': 'Error', 'message': "HTTP Service Error: "+str(e.getcode())}
            
            

app = Flask(__name__)

@app.route('/', methods = ['GET']('gateway']['smscs']['smsc']['id']))
def Servicehelp():
        return jsonify({'check-asterisk-status-at-default-path' :'/asterisk-status/', 'check-asterisk-status-at-custom-path': '/asterisk-status/?status-path=/etc/init.d/asterisk&cli-path=/usr/sbin/asterisk', 'check-kannel-status-at-default-path': '/kannel-status/' , 'check-kannel-status-at-custom-path': '/kannel-status/?status-path=/etc/init.d/kannel&web-port=13000'})
    
@app.route('/asterisk-status/', methods = [def asterisk_monitor():
        obj = AsteriskMonitor()
        if 'status-path' in request.args or 'cli-path' in request.args:
            obj.set_statusPath(request.args['status-path']('GET'])))
            obj.set_cliPath(request.args[        return jsonify(obj.get_serviceStatus())

@app.route('/kannel-status/', methods = ['GET']('cli-path'])))
def kannel_monitor():
        obj = KannelMonitor()
        if 'status-path' in request.args or 'web-port' in request.args:
            obj.set_statusPath(request.args[            obj.set_webPort(request.args['web-port']('status-path'])))
        return jsonify(obj.get_serviceStatus())
    
if __name__ == '__main__':
    app.run(host="0.0.0.0", port=8080, debug=False)
```

### Client Script (Nagios) 

```
$ python check_status.py -url http://X.X.X.X:8080/kannel-status/

OK: SMSC abc is Live!

$ python check_status.py -url http://X.X.X.X:8080/asterisk-status/

OK: Peer xyz is Live!
```

#### client.py 


```
#!/usr/bin/python

import urllib2, json, sys

usage = "usage: script.py [<ip-address-of-server>\n Note: value shouldn't be empty "
arguments = sys.argv

if '-url' in arguments and len(arguments) == 3:
    
    if arguments.index('-url') == 1:
        url = arguments[arguments.index('-url')+1](-url])
        
        try:
            connection = urllib2.urlopen(url)
            
            if connection.getcode() == 200:
                alert = json.loads(connection.read())
                connection.close()
                
                if alert[== "OK":
                    print alert['status']('status'])+": "+alert[                    sys.exit(0)
                    
                elif alert['status']('message']) == "Critical":
                    print alert["+alert['message']('status']+":)
                    sys.exit(2)
                    
                else:
                    print alert["+alert['message']('status']+":)
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
