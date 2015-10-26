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

        if status[0] == 0:
            if status[1].find('not running') != -1 or status[1].find('inactive') != -1 or status[1].find('failed') != -1:
                return {'status': 'Critical', 'message': 'Asterisk is not running!!!'}
                
            else:
                return self.get_peersStatus()
                
        else:
            return {'status': 'Error', 'message':  'Asterisk Status is Unknown!!!'}
    
    def get_peersStatus(self):
        peers_status = commands.getstatusoutput(self.cmd_for_peers_status)
        
        if peers_status[0] == 0:
            offline_peers = []
            online_peers = []
            unmonitored_peers = []
            peers = peers_status[1].split('\n')
                   
            for peer in peers:
                
                if peer.find('UNREACHABLE') != -1:
                    offline_peers.append(peer.split(" ")[0])
                elif peer.find('OK') != -1:
                    online_peers.append(peer.split(" ")[0])
                elif peer.find('Unmonitored') != -1:
                    unmonitored_peers.append(peer.split(" ")[0])
                    
            number_of_offline_peers = len(offline_peers)
            number_of_online_peers = len(online_peers)
            total_peers = number_of_offline_peers + number_of_online_peers
            channels_status = self.get_channelsStatus()
            unmonitored_peers.pop()
            
            if total_peers == 0:   
                return {'status': 'OK', 'message': 'No Peer Found!', 'channels': channels_status, 'unmonitored_peers': unmonitored_peers}
            
            elif total_peers == 1:
                if number_of_offline_peers == 1:
                    return {'status': 'Critical', 'message': 'Peer '+offline_peers[0]+' is dead!!!', 'channels': channels_status, 'unmonitored_peers': unmonitored_peers}
                elif number_of_online_peers == 1:
                    return {'status': 'OK', 'message': 'Peer '+online_peers[0]+' is Live!', 'channels': channels_status, 'unmonitored_peers': unmonitored_peers}
                     
            elif total_peers > 1:         
                if number_of_offline_peers > 1:
                    return {'status': 'Critical', 'message': str(number_of_offline_peers)+'/'+str(total_peers)+' Peers are offline!!!', 'online-peers': online_peers, 'offline-peers': offline_peers,  'channels': channels_status}
                    
                elif number_of_online_peers > 1:
                    return {'status': 'OK', 'message': 'All Peers are Live!', 'online-peers': online_peers, 'unmonitored_peers': unmonitored_peers, 'channels': channels_status}
           
        else:
            return {'status': 'Error', 'message': 'Peers are Unknown!!!'}
        
    def get_channelsStatus(self):
        channels_status = commands.getstatusoutput(self.cmd_for_active_channels)
        
        if channels_status[0] == 0:
            channels = channels_status[1].split('\n')
            
            for channel in channels:
                if channel.find('active channels') != -1:
                    active_channels = channel.split(" ")[0]
                elif channel.find('active calls') != -1:
                    active_calls = channel.split(" ")[0]
                elif channel.find('calls processed') != -1:
                    processed_calls = channel.split(" ")[0]
            
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

        if status[0] == 0:
            if status[1].find('running') == -1:
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
                
                kannel_status = response['gateway']['status'].split(',')[0]
                smscs_count = int(response['gateway']['smscs']['count'])
                
                if kannel_status == 'running':
                    
                    if smscs_count == 0:
                        return {'status': 'OK', 'message': 'No SMSC Found!'}
                   
                    elif smscs_count > 1:
                        
                        offline_smscs = []
                        online_smscs = []
                        smscs = response['gateway']['smscs']['smsc']
                    
                        for smsc in smscs:
                            smsc_status = smsc['status'].split(' ')[0]
                            
                            if smsc_status != 'online':
                                offline_smscs.append(smsc['id'])
                            else:
                                online_smscs.append(smsc['id'])
                        
                        number_of_offline_smscs = len(offline_smscs)
                        number_of_online_smscs = len(online_smscs)
                                    
                        if number_of_offline_smscs > 0:
                            return {'status': 'Critical', 'message': str(number_of_offline_smscs)+'/'+str(smscs_count)+' SMSCs are offline!!!', 'online-smscs': online_smscs, 'offline-smscs': offline_smscs }
                                
                        elif number_of_online_smscs > 0:
                            return {'status': 'OK', 'message': 'All SMSCs are Live!', 'online-smscs': online_smscs}
                            
                    else:
                        smsc_status = response['gateway']['smscs']['smsc']['status'].split(" ")[0]
                        smsc_id = response['gateway']['smscs']['smsc']['id']
                        
                        if smsc_status == 'online':
                            return {'status': 'OK','message': 'SMSC '+smsc_id+' is live!'}
                            
                        else:
                            return {'status': 'Critical','message': 'SMSC '+smsc_id+' is dead!!!'}
                            
                        
                else:
                    return {'status': 'Critical', 'message': 'Kannel is dead!!!'}
 
        except urllib2.HTTPError, e:
            return {'status': 'Error', 'message': "HTTP Service Error: "+str(e.getcode())}
            
            

app = Flask(__name__)

@app.route('/', methods = ['GET'])
def Servicehelp():
        return jsonify({'check-asterisk-status-at-default-path' :'/asterisk-status/', 'check-asterisk-status-at-custom-path': '/asterisk-status/?status-path=/etc/init.d/asterisk&cli-path=/usr/sbin/asterisk', 'check-kannel-status-at-default-path': '/kannel-status/' , 'check-kannel-status-at-custom-path': '/kannel-status/?status-path=/etc/init.d/kannel&web-port=13000'})
    
@app.route('/asterisk-status/', methods = ['GET'])
def asterisk_monitor():
        obj = AsteriskMonitor()
        if 'status-path' in request.args or 'cli-path' in request.args:
            obj.set_statusPath(request.args['status-path'])
            obj.set_cliPath(request.args['cli-path'])
        return jsonify(obj.get_serviceStatus())

@app.route('/kannel-status/', methods = ['GET'])
def kannel_monitor():
        obj = KannelMonitor()
        if 'status-path' in request.args or 'web-port' in request.args:
            obj.set_statusPath(request.args['status-path'])
            obj.set_webPort(request.args['web-port'])
        return jsonify(obj.get_serviceStatus())
    
if __name__ == '__main__':
    app.run(host="0.0.0.0", port=8080, debug=False)