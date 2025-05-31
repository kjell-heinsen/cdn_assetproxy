$.ajaxPrefilter(function(options, originalOptions,jqXHR){options.async = true});




function searcherror(){
        var formdata = {
            "search" : $('#error_search_input').val(),
        };

      //  $('#').text('Lädt....');

        var datas = { "formdata": JSON.stringify(formdata)};

        $.ajaxSetup({
            url : "/suche/search404main/",
            global:false,
            method: "POST",
            dataType: "json",
            asnyc:false,
        });


        $.ajax({
            data:datas
        })
            .done(function(data){
                if(data.status == 'ok'){
                //    console.log(data.links.linkresult.closest);
                    $('#box_searchresultslinks').removeAttr('hidden');
                    $('#searchresultslinks').html('Folgendes Ergebnis für die Suche von "'+data.links.linkresult.search+'" wurde gefunden: <a href="'+data.links.linkresult.closest.link+'">'+data.links.linkresult.closest.name+'</a>');
                }
                if(data.status == 'error'){

                }
            })
            .fail(function(data){

            })
            .always(function(data){

            });

}