//$(function () {
//    var fake_user_id = Math.floor((Math.random() * 1000) + 1);
//    window.app = {};
//    container = $('#notification-counter');
//    var count = parseInt(container.attr('data-count'));
//    var ids = [];
//    host = container.attr('data-host');
//    if (host === undefined) {
//        return false;
//    }
//    ws = new WebSocket('ws://' + host + ':8080');
//    app.BrainSocket = new BrainSocket(
//            ws,
//            new BrainSocketPubSub());
//
//    app.BrainSocket.Event.listen('generic.event', function (msg) {
//        response = msg.client.data.response;
//        if (response.length <= 0) {
//            return false;
//        }
//        handler = $('.sidebar--notifications');
//        for (var i = 0; i < response.length; i++) {
//            ids.push(response[i].id);
//            if ($('.notification-item[data-id="' + response[i].id + '"]').length <= 0) {
//                template = $(handler.find('.sidebar-item-template').html());
//                template.find('.item-content').html(response[i].value);
//                template.find('.item-title').html(response[i].name);
//                handler.find('.sidebar-content').append(template);
//                ++count;
//                $('.notification-counter').html(count);
//                $('#main-notifications .icon--alert');
//            }
//        }
//    });
//    setInterval(function () {
//        if (ws.readyState === 1) {
//            app.BrainSocket.message('generic.event', {
//                'message': ids,
//                'user_id': fake_user_id
//            });
//        }
//    }, 1000);
//});