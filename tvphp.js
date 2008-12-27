function setVisibilities(){
    var allshows = document.getElementsByClassName("Show");
    for(var i in allshows){
        var show = allshows[i];
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
    var allshows = document.getElementsByClassName("Show");
    for(var i in allshows){
        var show = allshows[i];
        if (show.childNodes && show.childNodes[3].textContent == name){
            show.channelOff = show.channelOff ? false : true;
        }
    }
    setVisibilities();
    return false;
}
