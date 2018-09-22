function selectData(obj){
    var selected = obj.value;
    $(".results").remove();

}

function imageV(obj){
    //alert("Please Login");
    var imageModal = document.getElementById('imageModal');
        var modalImg = document.getElementById("img01");
        

        imageModal.style.display = "block";
            var fullUrl = obj.src;
            var n = fullUrl.lastIndexOf("&");
            var url =fullUrl.substring(0,n);
            modalImg.src = url;
}

//$dataResponse = '<td><img onclick="imageV(this)" id="myImg" class="myImg" src="http://18.221.206.80:8080/ODKAggregate/view/binaryData?blobKey=build_Fyield-Data_1532407203[@version=null+and+@uiVersion=null]/data[@key='.$dataRow['_URI'].']/'.$modalName.'&previewImage=true" alt="image" width="50" "/></td>';