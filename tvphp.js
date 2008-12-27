function toggle(item){
    var n = item.nextSibling;
    while (n.className != "Infoa" && n.className != "Infob"){
        if(n.className == "Show" && n.childNodes[7].className == "ratings:0"){
            n.style.display = ((n.style.display == "none") ? "" : "None");
        }
        n = n.nextSibling;
    }
}

function channelSwitch(name){
    window.alert(name);
    return false;
}
