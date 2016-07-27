[
    {
        "id": "e77312ba.188cf",
        "type": "inject",
        "z": "tab9257988f9464a3a",
        "name": "Trigger %index%",
        "topic": "trigger",
        "payload": "timestamp",
        "payloadType": "date",
        "repeat": "",
        "crontab": "",
        "once": false,
        "x": 118.74999,
        "y": 53.75,
        "wires": [
            [
                "1d7f793f.e28087"
            ]
        ]
    },
    {
        "id": "1d7f793f.e28087",
        "type": "http request",
        "z": "tab9257988f9464a3a",
        "name": "HTTP Request",
        "method": "POST",
        "ret": "txt",
        "url": "http://v3.360medical.dev/ws/nodered/trigger/ansm.provider.processes/updateDoctypeAction",
        "x": 284.5,
        "y": 53,
        "wires": [
            [
                "7634e65e.89cb18"
            ]
        ]
    },
    {
        "id": "7634e65e.89cb18",
        "type": "debug",
        "z": "tab9257988f9464a3a",
        "name": "",
        "active": false,
        "console": "false",
        "complete": "false",
        "x": 466,
        "y": 52,
        "wires": []
    }
]
