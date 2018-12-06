$(function() {

    var showInfo = function(text, color) {
        $('#info').html(text).css('color', color);
    };

    $('#feedback-form').submit(function (e) {

        if (!navigator.onLine) {
            showInfo('Ваш запрос не отправлен - нет соединения с сетью', 'red');
        }

        var form = $(this);
        var url = form.attr('action');

        $.ajax({
            type: "POST",
            url: url,
            data: form.serialize(),
            success: function(data) {
                var data = JSON.parse(data);
                showInfo(data.text, data.status == 'ok' ? 'green' : 'red');
            },
            statusCode: {
                404: function () {
                    showInfo('Ваш запрос не отправлен - сервер не доступен', 'red');
                }
            }
        });

        e.preventDefault();
        
    });

});
