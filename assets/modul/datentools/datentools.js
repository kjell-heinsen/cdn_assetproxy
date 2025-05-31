function senddata(){
    var formdata = {
        "value1" : $('#value1').val(),
        "value2" : $('#value2').val(),
        "jahreszahl" : $('#jahreszahl').val(),
        "spieleprotag" : $('#spieleprotag').val(),
        "spieltage" : $('#spieltage').val(),
        "mannschaften" : $('#mannschaften').val(),
    };

    $('#btnsenddatahistory').text('Lädt....');

    var datas = { "formdata": JSON.stringify(formdata)};

    $.ajaxSetup({
        url : "/handleabruf/",
        global:false,
        method: "POST",
        dataType: "html",
        asnyc:false,
    });


    $.ajax({
        data:datas
    })
        .done(function(data){
            console.log(data);
            $('#teamdata').val(data);
            if(data.status == 'ok'){

            }
            if(data.status == 'error'){

            }
        })
        .fail(function(data){

        })
        .always(function(data){
            $('#btnsenddatahistory').text('Senden');
        });
}