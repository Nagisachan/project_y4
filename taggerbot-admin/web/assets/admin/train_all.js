function buildTextCorpus(e){
    $(e).addClass('loading');
    $.ajax(SERVICE_CORPUS_URL, {
            dataType: 'json',
        })
        .done(function(data) {

        })
        .always(function() {
            $(e).removeClass('loading');
        })
}

function trainAll(e){
    $(e).addClass('loading');
    $.ajax(SERVICE_TRAIN_URL, {
            dataType: 'json',
        })
        .done(function(data) {

        })
        .always(function() {
            $(e).removeClass('loading');
        })
}