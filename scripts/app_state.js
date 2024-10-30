if (window.navigator.standalone == true){


    var lastpage = localStorage.getItem('exitsatus');
    if (lastpage==null){
        lastpage = "/";
    } else {



}



    if(document.referrer.length > 0 && document.referrer.indexOf(document.location.host) != -1){
        var lastpageupdate = window.location;
        localStorage.setItem('exitsatus',lastpageupdate);      




    } else {
        window.location = lastpage;
    }





}
