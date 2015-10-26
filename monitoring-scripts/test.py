#!/usr/bin/python
import commands, sys

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
        


arguments = sys.argv
obj = AsteriskMonitor()
obj.set_statusPath(arguments[1])
obj.set_cliPath(arguments[2])
response  = obj.get_serviceStatus()

if response['status'] == 'Critical':
    print response['status']+": "+response['message']
    sys.exit(2)
else:
    print response['status']+": "+response['message']
    sys.exit(0)

        