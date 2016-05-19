#!/usr/bin/python

import urllib2, json, sys

usage = "usage: script.py [-i] <value of query parameter>\n Note: value shouldn't be empty "
arguments = sys.argv

if '-i' in arguments and len(arguments) == 3:
    
    if arguments.index('-i') == 1:
        query = arguments[arguments.index('-i')+1]
        
        try:
            connection = urllib2.urlopen("http://X.X.X.X:8888/monitor?query="+query)
            
            if connection.getcode() == 200:
                alert = json.loads(connection.read())
                connection.close()
                
                if alert['status'] == "Information":
                    print "OK: "+alert['title']+" is "+alert['value']+" "+alert['unit']
                    sys.exit(0)
                    
                elif alert['status'] == "Alert":
                    print alert['status']+": "+alert['title']+" is "+alert['value']+" "+alert['unit']
                    sys.exit(1)
                    
                elif alert['status'] == "Warning":
                    print alert['status']+": "+alert['title']+" is "+alert['value']+" "+alert['unit']
                    sys.exit(1)
                    
                elif alert['status'] == "Critical":
                    print alert['status']+": "+alert['title']+" is "+alert['value']+" "+alert['unit']
                    sys.exit(2)
                    
                else:
                    print alert['status']+": "+alert['value']
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
