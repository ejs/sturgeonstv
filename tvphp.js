function touch(url) {
    var tmp = new XMLHttpRequest();
    tmp.open("GET", url, true);
    tmp.send([]);
}

function setVisibilities() {
    var base = document.getElementById("ShowInformation");
    var allshows = base.childNodes[1].childNodes;
    var groupflag = '';
    var minrating = 0;
    for(var i in allshows) {
        var show = allshows[i];
        if (show.className == "Infoa" || show.className == "Infob") {
            groupflag = (show.showUnrated != undefined) ? show.showUnrated : (show.getAttribute("unrated") == "on");
            minrating = show.getAttribute("minrating");
        }
        else if(show.className == "Show") {
            var r = show.childNodes[7].className.charAt(show.childNodes[7].className.length-1);
            var channelName = show.childNodes[3].textContent;

            var rating = (r == 0 || minrating < r || minrating == r);
            var unrated = (r != 0 || groupflag);
            var channel = (base[channelName] == undefined) ? true : base[channelName];
            show.style.display = ( unrated && channel && rating) ? "" : "none";
        }
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
    touch('http://localhost/switchajax.php?to='+(base[name]?'on':'off')+'&channel='+name);
    document.getElementById(name).className = base[name] ? "active" : "inactive";
    setVisibilities();
    return false;
}

function setRating(rating, name) {
    setVisibilities();
    var allshows = document.getElementById("ShowInformation").childNodes[1].childNodes;
    for(var i in allshows) {
        if (allshows[i].className == "Show" && allshows[i].childNodes[5].textContent == name) {
            var ratingdisplay = allshows[i].childNodes[7];
            ratingdisplay.className = "ratings:"+rating;
            for(var j in [0, 1, 2, 3, 4, 5]) {
                var img = ratingdisplay.childNodes[''+j].childNodes['0'];
                if(j == 0)
                    img.src = 'x.png';
                else
                    img.src = (j <= rating) ? 'black.png' : 'white.png';
            }
        }
    }
    touch("http://localhost/setajax.php?show="+name+"&rating="+rating);
    return false;
}
