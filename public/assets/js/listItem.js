
$(document).ready(function() {
    
    $.ajaxSetup({
        header: {
            'X-CSRF-TOKEN': $("meta[name='csrf-token']").attr('content')
        }
    });

    $.ajax({

        url: "{{url('/saveItem')}}",
        method: 'POST',
        data: {
            name: 'ajax test list item'
        },
        success: function() {
            console.log('IT WORKED')
        }
    });

})

function create_item(listid, inputid) {

    const name = document.getElementById(inputid).value;

    if (name == null) {
        return;
    }

    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
        if (xmlhttp.readyState == 4 && xmlhttp.status == 200) {
            var data = xmlhttp.responseText;

            if (data != false) {

            }
            return;
        }
    }

    var params = `name=${name}`;
    xmlhttp.open("POST", "/saveItem", true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send(params);

}