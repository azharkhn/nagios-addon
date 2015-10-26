#!/usr/bin/python

import xmltodict, urllib2, sys

link_tag = '-l'
usage = "usage: script.py ["+link_tag+"] <url-of-kannel-status>\n Note: value shouldn't be empty "
arguments = sys.argv

if link_tag in arguments and len(arguments) == 3:
 
    if arguments.index(link_tag) == 1:
        url = arguments[arguments.index(link_tag)+1]
 
        try:
            connection = urllib2.urlopen(url)
            
            if connection.getcode() == 200:
                response = xmltodict.parse(connection.read())
                connection.close()
                
                kannel_status = response['gateway']['status'].split(',')[0]
                smscs_count = int(response['gateway']['smscs']['count'])
                
                if kannel_status == 'running':
                    
                    if smscs_count > 1:
                        
                        smscs_offline = []
                        smscs = response['gateway']['smscs']['smsc']
                    
                        for smsc in smscs:
                            smsc_status = smsc['status'].split(' ')[0]
                            
                            if smsc_status != 'online':
                                smscs_offline.insert(smsc['id'])
                        
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
                        smsc_status = response['gateway']['smscs']['smsc']['status'].split(" ")[0]
                        smsc_id = response['gateway']['smscs']['smsc']['id']
                        
                        if smsc_status == 'online':
                            uptime = response['gateway']['smscs']['smsc']['status'].split(" ")[1]
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
