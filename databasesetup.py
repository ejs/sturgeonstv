
#!/Library/Frameworks/Python.framework/Versions/Current/bin/python
import re
import feedparser
import MySQLdb
from datetime import datetime, date, time, timedelta
import sys

sys.path.append("/Library/Frameworks/Python.framework/Versions/Current/lib/python2.6/site-packages")

def read_PHP_config_file(fn):
    pattern = re.compile("\s*\$(\w+)\s+=\s+(?:['|\"])(\w*)(?:['|\"];)")
    config = {}
    for line in open(fn, 'r'):
        tmp = pattern.match(line)
        if tmp:
            key, value = tmp.groups()
            config[key] = value
    return config

data = read_PHP_config_file('config.php')
db = MySQLdb.connect(host=data['databaseserver'], user=data['databaseuser'], passwd=data['databasepassword'], db=data['databasename'])
cursor = db.cursor()


cursor.execute("""CREATE TABLE channel (
        channelname VARCHAR(20),
        url VARCHAR(90),
        lastchecked DATETIME,
        storeddays INT,
        marker INT,
        standard TINYINT,
    );"""

cursor.execute("""CREATE TABLE user (
        username VARCHAR(20),
        email VARCHAR(40),
        password VARCHAR(40),
        varified TINYINT,
        created DATETIME,
        lastused DATETIME,
    );"""

cursor.execute("""CREATE TABLE userchannels (
        username VARCHAR(20),
        channelname VARCHAR(20),
        state INT,
        set_on DATETIME,
    );"""

cursor.execute("""CREATE TABLE tvshow (
        showname VARCHAR(100),
        average_rating double,
    );"""

cursor.execute("""CREATE TABLE tvshowinstance (
        starttime DATETIME,
        endtime DATETIME,
        showname VARCHAR(100),
        channelname VARCHAR(20),
        discription VARCHAR(200),
        url VARCHAR(100),
    );"""

cursor.execute("""CREATE TABLE tvshowrating (
        username VARCHAR(20),
        showname VARCAHR(100),
        rating INT,
        lastset DATETIME,
    );"""
