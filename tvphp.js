function getObjectText(obj){ return document.all ? obj.innerText : obj.textContent; }

function touch(url) {
    var tmp = new XMLHttpRequest();
    tmp.open("GET", url, true);
    tmp.send([]);
}

function getChildClassed(item, classPattern){
    for (var i in item.childNodes){
        var point = item.childNodes[i];
        if(point.className && point.className.search(classPattern) != -1)
            return point;
    }
    window.alert('FAIL');
    return [];
}

function setVisibilities() {
    var base = document.getElementById("ShowInformation");
    var allshows = getChildClassed(base, "tbody").childNodes;
    var groupflag = '';
    var minrating = 0;
    for(var i in allshows) {
        var show = allshows[i];
        if (show.className == "Infoa" || show.className == "Infob") {
            groupflag = (show.showUnrated != undefined) ? show.showUnrated : (show.getAttribute("unrated") == "on");
            minrating = show.getAttribute("minrating");
        }
        else if(show.className == "Show") {
            var r = getChildClassed(show, /rating/);
            var r = r.className.charAt(r.className.length-1);
            var channelName = getObjectText(getChildClassed(show, "channel"));

            var rating = (r == 0 || minrating < r || minrating == r);
            var unrated = (r != 0 || groupflag);
            var channel = (base[channelName] == undefined) ? true : base[channelName];
            show.style.display = ( unrated && channel && rating) ? "" : "none";
        }
    }
}

function getTime(item, name) {
    var starttime = getChildClassed(item, /time/).getAttribute(name).split(':');
    var starttime = new Date(starttime[2], starttime[1], starttime[0], starttime[3], starttime[4], 0, 0);
    return starttime;
}

function hover(item) {
    var start = getTime(item, "starttime");
    var end = getTime(item, "endtime");
    var allshows = getChildClassed(document.getElementById("ShowInformation"), "tbody").childNodes;
    for(var i in allshows) {
        var show = allshows[i];
        if(show.className == "Show") {
            var s = getTime(show, "starttime");
            var e = getTime(show, "endtime");
            if ((s <= start && start < e) || (s < end && end <= e) || (start <= s && e <= end))
                show.style.background = "#ddd";
        }
    }
    item.style.background = "#888";
}

function leave(item) {
    var allshows = getChildClassed(document.getElementById("ShowInformation"), "tbody").childNodes;
    for(var i in allshows) {
        var show = allshows[i];
        if(show.className == "Show")
            show.style.background = "";
    }
}

function toggle(item) {
    if (item.showUnrated != undefined)
        item.showUnrated = item.showUnrated ? false : true;
    else
        item.showUnrated = !(item.getAttribute("unrated") == "on");
    setVisibilities();
}

function channelSwitch(name) {
    var base = document.getElementById("ShowInformation");
    base[name] = !(base[name] == undefined || base[name]);
    touch(""+location.protocol+"//"+location.host+'/switch.php?to='+(base[name]?'on':'off')+'&channel='+name+'&ajax=1');
    document.getElementById(name.replace(/ /g, '_')).className = base[name] ? "active" : "inactive";
    setVisibilities();
    return false;
}

last = '';

function setRating(rating, name) {
    if (name != last)
        setVisibilities();
    last = name;
    var allshows = getChildClassed(document.getElementById("ShowInformation"), /tbody/).childNodes;
    for(var i in allshows) {
        if (allshows[i].className == "Show" && getObjectText(getChildClassed(allshows[i], /show/)) == name) {
            var ratingdisplay = getChildClassed(allshows[i], /rating/);
            ratingdisplay.className = "ratings:"+rating;
            for(var j in [0, 1, 2, 3, 4, 5]) {
                var img = getChildClassed(getChildClassed(ratingdisplay, j), /img/);
                if(j == 0)
                    img.src = 'x.png';
                else
                    img.src = (j <= rating) ? 'black.png' : 'white.png';
            }
        }
    }
    touch(""+location.protocol+"//"+location.host+"/set.php?show="+escape(name)+"&rating="+rating+"&ajax=1");
    return false;
}
