#!/usr/bin/python

from flask import Flask, jsonify, request
from flask_restful import abort
import phonenumbers, urllib2

class NumberDetails(object):
    phonenumber = ''
    phonenumber_object = ''
    country_iso = ''
    phonenumber_details = {}
    phonenumber_types = {0:'FIXED_LINE',1:'MOBILE',2:'FIXED_LINE_OR_MOBILE',3:'TOLL_FREE',4:'PREMIUM_RATE',5:'SHARED_COST',6:'VOIP',7:'PERSONAL_NUMBER',8:'PAGER',9:'UAN',10:'VOICEMAIL',99:'UNKNOWN'}

    
    def set_phonenumber(self,phonenumber):
        self.phonenumber = self.get_WithoutE164format(phonenumber)
        self.country_iso = str(phonenumbers.region_code_for_number(phonenumbers.parse(self.get_E164format(self.phonenumber), None)))
        self.phonenumber_object = phonenumbers.parse(self.get_E164format(self.phonenumber), self.country_iso)
                        
    def get_phoneNumberDetails(self):
        self.phonenumber_details['phonenumber'] = self.phonenumber
        self.phonenumber_details['nsn'] = str(phonenumbers.national_significant_number(self.phonenumber_object))
        self.phonenumber_details['iso'] = self.country_iso
        self.phonenumber_details['code'] = self.phonenumber[:len(self.phonenumber)-len(self.phonenumber_details['nsn'])]
        self.phonenumber_details['typeid'] = phonenumbers.number_type(self.phonenumber_object)
        return self.phonenumber_details
        
        
    def validatePhoneNumber(self):
        return phonenumbers.is_valid_number(self.phonenumber_object) * phonenumbers.is_possible_number(self.phonenumber_object) * phonenumbers.is_valid_number_for_region(self.phonenumber_object,self.country_iso)

    def get_E164format(self, phonenumber):
        phonenumber = phonenumber.replace('+', '')
        if(phonenumber[:2] == '00'):
            return '+'+phonenumber[2:]
        elif(phonenumber[:1] == '0'):
            return '+'+phonenumber[1:]
        else:
            return '+'+phonenumber  
        
    
    def get_WithoutE164format(self,phonenumber):
        phonenumber = phonenumber.replace('+', '')
        if(phonenumber[:2] == '00'):
            return phonenumber[2:]
        elif(phonenumber[:1] == '0'):
            return phonenumber[1:]
        else:
            return phonenumber


class SMSSent:
    smsc = ''
    src = ''
    dst = ''
    text = ''
    
    def set_smscID(self, smsc):
        if smsc != '' and smsc != None:
            self.smsc = smsc
        else:
            self.smsc = False
        
    def set_srcNumber(self, src):
        if src != '' and src != None:
            self.src = src
        else:
            self.src = False
        
    def set_dstNumber(self, dst):
        if dst != '' and dst != None:
            obj = NumberDetails()
            obj.set_phonenumber(dst)
            details = obj.get_phoneNumberDetails()
            
            if details['iso'] == 'PK' and obj.validatePhoneNumber():
                
                if details['typeid'] == 1 or details['typeid'] == 2:
                    self.dst = dst
                
                else:
                    self.dst = False
            else:
                self.dst = False
        else:
            self.dst = False
    
    def set_messageText(self, text):
        if text != '' and text != None:
            self.text = text
        else:
            self.text = False
    
    def get_response(self):
        if self.smsc != False and self.src != False and self.dst != False and self.text != False:
            
            url = "http://127.0.0.1:13013/cgi-bin/sendsms?smsc="+self.smsc+"&username=userinfo&password=ac4737ae&from="+self.src+"&to="+self.dst+"&text="+self.text
                
            try:
                connection = urllib2.urlopen(url)
                response = connection.read()
                connection.close()
                        
                return {'status': connection.getcode(), 'message': response}
               
            except urllib2.HTTPError, e:
                return {'status': connection.getcode(), 'message': "HTTP Service Error: "+str(e.getcode())}
            
        else:
            return {'status': 405, 'message': "Phone Number not allowed"}

app = Flask(__name__)

@app.route('/sms-sent', methods = ['GET'])
def sms_sent():
    if 'smsc' in request.args and 'from' in request.args and 'to' in request.args and 'text' in request.args:
        
        smsc = request.args['smsc']
        src = request.args['from']
        dst = request.args['to']
        text = request.args['text']
        
        obj = SMSSent()
        obj.set_smscID(smsc)
        obj.set_srcNumber(src)
        obj.set_dstNumber(dst)
        obj.set_messageText(text)
        
        return jsonify(obj.get_response())
        
    else:
        abort(404)
        
if __name__ == '__main__':
    app.run(host="0.0.0.0", port=8080, debug=True)
