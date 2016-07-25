/**
 * Copyright 2013,2015 IBM Corp.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */
module.exports = function(RED) {
    "use strict";
    var spawn = require('child_process').spawn;

    function runNode(n) {
        RED.nodes.createNode(this, n);
        this.cmd = (n.command || "").trim();

        var node = this;
        this.on('input', function(msg) {
            node.status({ fill: 'blue', shape: 'dot', text: 'Spawning process...' });

            var usrCmd  = msg.payload.cmd,
                usrArgs = msg.payload.args;

            // push casperjs script path as first argument
            var argz = [];
            argz.push(usrCmd);

            // push other usr arguments
            for (var prop in usrArgs) {
                if (usrArgs.hasOwnProperty(prop)) {
                    var arg = '--' + prop + '=' + usrArgs[prop];
                    argz.push(arg);
                }
            }
            
            // spawn a child process
            var ex = spawn(this.cmd, argz);

            // output and error handling
            ex.stdout.on('data', function (data) {
                data = data.toString();
                msg.payload = data;
                node.send([msg, null, null]);
            });

            ex.stderr.on('data', function (data) {
                data = data.toString();
                msg.payload = data;
                node.send([null, msg, null]);
            });

            // on process close
            ex.on('close', function (code) {
                msg.payload = code;
                node.send([null, null, msg]);
            });

            // on spawn error
            ex.on('error', function (code) {
                node.error(code, msg);
            });

            node.status({});
        });
    }
    RED.nodes.registerType('run', runNode);
}
