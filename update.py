import re
import feedparser
import MySQLdb
from datetime import datetime, date, time, timedelta


def read_PHP_config_file(fn):
    pattern = re.compile("\s*\$(\w+)\s+=\s+(?:['|\"])(\w*)(?:['|\"];)")
    config = {}
    for line in open(fn, 'r'):
        tmp = pattern.match(line)
        if tmp:
            key, value = tmp.groups()
            config[key] = value
    return config


def find_shows(url, offset, daystart):
    print daystart
    source = feedparser.parse(url).entries
    assert len(source)
    day = date.today()+timedelta(days=offset)
    start = datetime.combine(day, time()) + daystart
    for show in source:
        data =  show.title.split(':')
        title = ':'.join(data[1:])
        start_time = data[0]
        h, m = int(start_time[:2]) - daystart.seconds//(3600), int(start_time[2:])- (daystart.seconds//60)%60
        if h < 0: # adjust to take into acount a new day starting at 6am
            h += 24
        start_time = start + timedelta(hours=h, minutes=m)
        title = title.strip()
        description = show.summary
        link = show.link
        yield start_time, title, description, link


def updateChannel(connection, name, url, lastchecked, storeddays, marker, daystart):
    print name, lastchecked, storeddays, marker
    try:
        for start, title, description, link in find_shows(url[:-1]+str(storeddays), storeddays, daystart):
            connection.execute('INSERT tvshowinstance SET showname="%s", channelname="%s", starttime="%s", discription="%s", url="%s";'%(title, name, start, description, link))
    except AssertionError:
        print "Empty Feed"
        connection.execute('UPDATE channel SET marker="%s", lastchecked=NOW() WHERE channelname ="%s";'%(marker+1, name))
    except Exception, e:
        # catch and log warnings from MySQL as well
        print type(e)
        print e
    else:
        connection.execute('UPDATE channel SET storeddays="%s", lastchecked=NOW() WHERE channelname ="%s";'%(storeddays+1, name))


def updateDate(connection, name, lastchecked, storeddays, daystart):
    last = date(lastchecked.year, lastchecked.month, lastchecked.day)
    age = date.today() - last
    delta = age.days
    if delta:
        print "Moving channel ", name, " last updated ", lastchecked, " back ", delta, "days."
        connection.execute('UPDATE channel SET marker=0, storeddays="%s" WHERE channelname ="%s";'%(storeddays-delta, name))


data = read_PHP_config_file('config.php')
db = MySQLdb.connect(host=data['databaseserver'], user=data['databaseuser'], passwd=data['databasepassword'], db=data['databasename'])

cursor = db.cursor()

cursor.execute("SELECT channelname, lastchecked, storeddays, daystart FROM channel WHERE lastchecked < CURDATE() ORDER BY lastchecked")
result = cursor.fetchall()
if result:
    updateDate(cursor, *result[0])

cursor.execute("SELECT channelname, url, lastchecked, storeddays, marker, daystart FROM channel WHERE marker < 3 ORDER BY marker, lastchecked")
result = cursor.fetchall()
if result:
    updateChannel(cursor, *result[0])
