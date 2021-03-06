[
  {
    "id": "9bb91265.ed4c4",
    "type": "http in",
    "z": "3da45b3.3c4e9a4",
    "name": "Input",
    "url": "\/adadgio\/loop\/start\/%index%",
    "method": "post",
    "swaggerDoc": "",
    "x": 268,
    "y": 118,
    "wires": [
      [
        "bfba8f48.c9ac1"
      ]
    ]
  },
  {
    "id": "bfba8f48.c9ac1",
    "type": "function",
    "z": "3da45b3.3c4e9a4",
    "name": "Go on",
    "func": "\nreturn [msg,msg];",
    "outputs": "2",
    "x": 404.85715702602,
    "y": 145.57143224988,
    "wires": [
      [
        "f4861d61.a396b"
      ],
      [
        "d81d4340.60395"
      ]
    ]
  },
  {
    "id": "f4861d61.a396b",
    "type": "http response",
    "z": "3da45b3.3c4e9a4",
    "name": "Answer for information",
    "x": 610.42854309082,
    "y": 108.57144165039,
    "wires": [

    ]
  },
  {
    "id": "d81d4340.60395",
    "type": "http request",
    "z": "3da45b3.3c4e9a4",
    "name": "",
    "method": "POST",
    "ret": "obj",
    "url": "%protocol%%domain%\/adadgio\/loop\/worker",
    "x": 577.4285736084,
    "y": 181.57139587402,
    "wires": [
      [
        "e32d8c17.d97ce"
      ]
    ]
  },
  {
    "id": "1c18c8ed.658087",
    "type": "debug",
    "z": "3da45b3.3c4e9a4",
    "name": "CONTINUE",
    "active": false,
    "console": "false",
    "complete": "payload",
    "x": 749.17844608852,
    "y": 218.74998869214,
    "wires": [

    ]
  },
  {
    "id": "4018482.56529b8",
    "type": "debug",
    "z": "3da45b3.3c4e9a4",
    "name": "KILL",
    "active": false,
    "console": "false",
    "complete": "payload",
    "x": 733.28574589321,
    "y": 271.7500201634,
    "wires": [

    ]
  },
  {
    "id": "e32d8c17.d97ce",
    "type": "switch",
    "z": "3da45b3.3c4e9a4",
    "name": "",
    "property": "payload.kill",
    "rules": [
      {
        "t": "false"
      },
      {
        "t": "else"
      }
    ],
    "checkall": "true",
    "outputs": 2,
    "x": 574.07139042446,
    "y": 265.32146181379,
    "wires": [
      [
        "d81d4340.60395",
        "1c18c8ed.658087"
      ],
      [
        "4018482.56529b8"
      ]
    ]
  }
]
