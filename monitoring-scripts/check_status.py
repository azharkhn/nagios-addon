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
