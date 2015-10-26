#!/usr/bin/python

from flask import Flask, jsonify, request
from flask_restful import abort
from lib.MonitoringAlerts import Notifications
    
app = Flask(__name__)

@app.route('/monitor', methods = ['GET'])
def monitor():
    if 'query' in request.args:
        obj = Notifications();
        obj.set_webServiceQuery(request.args['query'])
        return jsonify(obj.get_webServiceResponse())

    else:
        abort(404)
    
if __name__ == '__main__':
    app.run(host="0.0.0.0", port=8888, debug=True)
