# coding=utf-8
# webhook.py
import os
def application(environ, start_response):
    start_response('200 OK', [('Content-Type', 'text/html')])
    # os.system('git add .')
    # os.system('git commit -m "merge"')
    os.system('git pull')
    print('git pull finish')
    return [b'Hello, webhook!']