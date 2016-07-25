/**
 * webshoe node
 */
module.exports = function(RED) {
"use strict";
    var ws = require('/usr/local/lib/node_modules/ws').Server;
    //var inspect = require("util").inspect;

    function WebSocketPackage(config) {
        RED.nodes.createNode(this,config);
        var node = this;

        var socket = new ws('/truc');

        this.on('input', function(msg) {
            msg.payload = msg.payload.toLowerCase();
            node.send(msg);
        });
    };

    RED.nodes.registerType("webshoe", WebSocketPackage);
}
// module.exports = function(RED) {
// "use strict";
//
//     var ws = require("ws");
//     var inspect = require("util").inspect;
//
//     function WebSocketPackage(n) {
//         RED.nodes.createNode(this,n);
//         var node = this;
//
//         var socket = new ws('/truc');
//         node.server = socket;
//
//         socket.on('message', function(data, flags) {
//
//         };
//         socket.on('close',function() {
//
//         };
//         socket.on('error', function(err) {
//
//         };
//     }
//
//     // function LowerCaseNode(config) {
//     //     RED.nodes.createNode(this,config);
//     //
//     //     var node = this;
//     //
//     //     this.on('input', function(msg) {
//     //         msg.payload = msg.payload.toLowerCase();
//     //         node.send(msg);
//     //     });
//     // }
//
//     RED.nodes.registerType("webshoe-one", WebSocketPackage);
// }
