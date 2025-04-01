import os, sys
from http.server import HTTPServer, CGIHTTPRequestHandler
webdir = '.'  # каталог с файлами HTML и подкаталогом cgi-bin
port = 80     # порт по умолчанию
if len(sys.argv) > 1: webdir = sys.argv[1]
if len(sys.argv) > 2: port = int(sys.argv[2])
print('webdir "%s", port %s' % (webdir, port))
os.chdir(webdir)
srvraddr = ('', port)
srvrobj = HTTPServer(srvraddr, CGIHTTPRequestHandler)
srvrobj.serve_forever()