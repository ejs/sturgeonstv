function touch(url){
    window.alert(url);
    var tmp = new XMLHttpRequest();
    tmp.open("GET", url, true);
    tmp.onreadystatechange = function(){
        if (tmp.readyState == 4){
            var a = 1;
        }
    };
    tmp.send([]);
}

function setVisibilities(){
    var base = document.getElementById("ShowInformation");
    var allshows = base.childNodes[1].childNodes;
    var groupflag = '';
    for(var i in allshows){
        var show = allshows[i];
        if (show.className == "Infoa" || show.className == "Infob") {
            groupflag = (show.showUnrated != undefined) ? show.showUnrated : true;
        }
        else if(show.className == "Show"){
            var unrate = (show.childNodes[7].className == "ratings:0") ? groupflag : true;
            var channelName = show.childNodes[3].textContent;
            var channel = (base[channelName] == undefined) ? true : base[channelName];
            show.style.display = (unrate && channel) ? "" : "none";
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
    var base = document.getElementById("ShowInformation");
    if(base[name] == undefined || base[name]) {
        base[name] = false;
        touch('http://localhost/switchajax.php?to=off&channel='+name);
    }
    else{
        base[name] = true;
        touch('http://localhost/switchajax.php?to=on&channel='+name);
    }
    // update local display
    setVisibilities();
    return false;
}
