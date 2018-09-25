function selectData(obj){
    var selected = obj.value;
    $(".results").remove();
}

function imageV(obj){
        var data_uri = obj.getAttribute('data-uri');
        var data_table = obj.getAttribute('data-table');
             typeData(data_uri,data_table,obj);           
}

function typeData(data_uri,data_table,obj){

                 $.ajax({
            url: 'contentType.php',
            method: 'POST',
            dataType: 'json',
            data: {
             uri_value: data_uri,
             table_name: data_table
           },
           success: function(response) {
            var a =response.dataTypeElement;
            modalShow(a,obj);
            },
            error: function(error) {
          alert('Error occurs!'+JSON.stringify(error));
       }
        });
}
//class="" id="";
function modalShow(type,obj){
    if(type == "image/jpeg"){
        var imageModal = document.getElementById('imageModal');
        
        var modalImage = document.createElement("img");
        modalImage.setAttribute("class","imageModalData-content");
        modalImage.setAttribute("id","img01");
        imageModal.appendChild(modalImage);

        var spanClose = document.createElement("span");
        spanClose.innerHTML = '&times;';
        spanClose.setAttribute("class","imageDataClose");
        spanClose.setAttribute("id", "spanId");
        spanClose.setAttribute("onclick","spanClick(this)");
        imageModal.appendChild(spanClose);

        imageModal.style.display = "block";
            var fullUrl = obj.src;
            var n = fullUrl.lastIndexOf("&");
            var imageUrl =fullUrl.substring(0,n);
            modalImage.src = imageUrl;
    }
    else if(type == "video/mp4"){
        var videoModal = document.getElementById('imageModal');
        var modalVideo = document.createElement("video");
        modalVideo.setAttribute("class","imageModalData-content");
        var spanClose = document.createElement("span");
        spanClose.innerHTML = '&times;';
        spanClose.setAttribute("class","imageDataClose");
        spanClose.setAttribute("id", "spanId");
        spanClose.setAttribute("onclick","spanClick(this)");
        videoModal.appendChild(spanClose);

        var source = document.createElement("source");
        source.setAttribute("id", "img01");
        
     
        videoModal.appendChild(modalVideo);
        modalVideo.appendChild(source);
        modalVideo.setAttribute("controls","controls");
        modalVideo.setAttribute("autoplay","autoplay");

        videoModal.style.display = "block";
            var fullUrl = obj.src;
            var n = fullUrl.lastIndexOf("&");
            var videoUrl =fullUrl.substring(0,n);
            source.src = videoUrl;
    }
    else{
        alert("Type not confirmed");
    }
}

function spanClick(obj){
     var imageModal = document.getElementById('imageModal');
        var modalImg = document.getElementById("img01");
        
          document.getElementById("imageModal").innerHTML="";
          imageModal.style.display = "none";
}
