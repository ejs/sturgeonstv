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
    var allshows = document.getElementById("ShowInformation").childNodes[1].childNodes;
    var groupflag = '';
    for(var i in allshows){
        var show = allshows[i];
        if (show.className == "Infoa" || show.className == "Infob") {
            groupflag = (show.showUnrated != undefined) ? show.showUnrated : true;
        }
        else if(show.className == "Show"){
            var unrate = (show.childNodes[7].className == "ratings:0") ? groupflag : true;
            var channel = (show.channelOff == undefined ? false : show.channelOff);
            show.style.display = (unrate && !channel) ? "" : "none";
        }
    }
}

function toggle(item){
    if (item.showUnrated != undefined)
        item.showUnrated = item.showUnrated ? false : true;
    else
        item.showUnrated = false;
    setVisibilities();
}

function channelSwitch(name){
    setVisibilities();
    touch('http://localhost/switchajax.php?to=off&channel='+name);
    return false;
}
