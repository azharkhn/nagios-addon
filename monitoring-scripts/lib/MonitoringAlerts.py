#!/usr/bin/env python
import smtplib
from iniparse import INIConfig
from DataBase import MySQL

class Notifications:
    cfg = INIConfig(open('/var/www/html/monitoring-scripts/lib/config.ini'))
    query = ''
    
    def set_webServiceQuery(self, query):
            self.query = query
    
    def get_webServiceResponse (self):
        if self.query != None or self.query != '':
            objDB = MySQL()
            main_connection = objDB.connect(self.cfg.database.hostName, self.cfg.database.userName, self.cfg.database.passWord, self.cfg.database.dbName, int(self.cfg.database.port))
    
            if main_connection == True:
                notification = objDB.fetchrows("SELECT * FROM monitoring_thresholds WHERE active='Y' AND title='"+self.query+"';", 1)
                if notification != None:
                    dbid = int(notification[1])
                    title = notification[2]
                    vendor = notification[3]
                    alert = float(notification[4])
                    warning = float(notification[5])
                    critical = float(notification[6])
                    sql = notification[7]
                    unit = notification[8]
                    description = notification[9]
                    
                    database = objDB.fetchrows("SELECT * FROM monitoring_db WHERE active='Y' AND id="+str(dbid)+";", 1)
                    if database != None:
                        sDB = MySQL()
                        connection = sDB.connect(database[2], database[3], database[4], database[6], int(database[5]))
                        
                        if connection == True:
                            result = sDB.fetchrows(sql, 1)
                            if result != None and result[0] != None:
                                result = float("{0:.3f}".format(result[0]))
                                
                                if alert > warning and warning > critical and alert > critical:
                                    if result <= critical:
                                        return {'title': title, 'vendor': vendor, 'status' : 'Critical', 'value': str(result), 'unit': unit, 'description' : description}
                                    elif result > critical and result <= warning:
                                        return {'title': title, 'vendor': vendor, 'status' : 'Warning', 'value': str(result), 'unit': unit, 'description' : description}
                                    elif result > warning and result <= alert:
                                        return {'title': title, 'vendor': vendor, 'status' : 'Alert', 'value': str(result), 'unit': unit, 'description' : description}
                                    else:
                                        return {'title': title, 'vendor': vendor, 'status' : 'Information', 'value': str(result), 'unit': unit, 'description' : description}
                                
                                elif alert < warning and warning < critical and alert < critical:
                                    if result >= critical:
                                        return {'title': title, 'vendor': vendor, 'status' : 'Critical', 'value': str(result), 'unit': unit, 'description' : description}
                                    elif result < critical and result >= warning:
                                        return {'title': title, 'vendor': vendor, 'status' : 'Warning', 'value': str(result), 'unit': unit, 'description' : description}
                                    elif result < warning and result >= alert:
                                        return {'title': title, 'vendor': vendor, 'status' : 'Alert', 'value': str(result), 'unit': unit, 'description' : description}
                                    else:
                                        return {'title': title, 'vendor': vendor, 'status' : 'Information', 'value': str(result), 'unit': unit, 'description' : description}
                                else:
                                    return {'status' : 'Error', 'value': 'UNKNOWN-STATUS'}
                                    
                            else:
                                return {'title': title, 'vendor': vendor, 'status' : 'Information', 'value': 'DATA-NOT-AVAILABLE', 'unit': unit, 'description' : description}
                            sDB.close()
                            
                        else:
                            return {'status' : 'Error', 'value': connection}
                    else:
                        return {'status' : 'Error', 'value': 'DATABASE-NOT-FOUND!'}
                else:
                    return {'status' : 'Error', 'value': 'QUERY-NOT-FOUND!'}
                        
                objDB.close()
            else:
                return {'status' : 'Error', 'value': main_connection}
        else:
                return {'status' : 'Error', 'value': 'QUERY-NOT-FOUND!'}
    
    def sendEmail (self, title, receivers, alert_type, vendor, value):
        sender = self.cfg.email.fromAddress
       
        message = """From: Azhar Nawaz <azhar.nawaz@vopium.com>
        MIME-Version: 1.0
        Content-type: text/html
        Subject: """+title+"""
        
        <b>This is HTML message.</b><br>
        <h1>This is headline.</h1>
        """
        
        smtpObj = smtplib.SMTP_SSL(self.cfg.email.hostName, self.cfg.email.port)
        smtpObj.login(self.cfg.email.userName, self.cfg.email.passWord)
        smtpObj.sendmail(sender, receivers, message)         
        print "Successfully sent email"
        smtpObj.close()
        
        


        