function touch(url){
    window.alert(url);
    var tmp = new XMLHttpRequest();
    tmp.open("GET", url, true);
    tmp.onreadystatechange = function(){
        if (tmp.readyState == 4){
            window.alert(tmp.document);
        }
    };
    tmp.send([]);
}

function setVisibilities(){
    var allshows = document.getElementById("ShowInformation").childNodes[1];
    for(var i in allshows.childNodes){
        var show = allshows.childNodes[i];
        var unrated = (show.showRating == undefined ? true : show.showRating);
        var channel = (show.channelOff == undefined ? false : show.channelOff);
        if (show.style)
            show.style.display = (unrated && !channel) ? "" : "none";
    }
}

function toggle(item){
    var n = item.nextSibling;
    if (item.showUnrated != undefined)
        item.showUnrated = item.showUnrated ? false : true;
    else
        item.showUnrated = false;
    while (n.className != "Infoa" && n.className != "Infob"){
        if(n.className == "Show" && n.childNodes[7].className == "ratings:0")
            n.showRating = item.showUnrated;
        n = n.nextSibling;
    }
    setVisibilities();
}

function channelSwitch(name){
    var allshows = document.getElementsById("ShowInformation").childNodes[1];
    for(var i in allshows.childNodes){
        var show = allshows[i].childNodes;
        if (show.childNodes && show.childNodes[3].textContent == name){
            show.channelOff = show.channelOff ? false : true;
        }
    }
    setVisibilities();
    touch('http://localhost/switchajax.php?to=off&channel='+name);
    return false;
}
