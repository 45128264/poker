
var pokerdata =   [
    {"type" : "heart", "value" : "3",  "level" : 1  },
    {"type" : "heart", "value" : "4",  "level" : 2  },
    {"type" : "heart", "value" : "5",  "level" : 3  },
    {"type" : "heart", "value" : "6",  "level" : 4  },
    {"type" : "heart", "value" : "7",  "level" : 5  },
    {"type" : "heart", "value" : "8",  "level" : 6  },
    {"type" : "heart", "value" : "9",  "level" : 7  },
    {"type" : "heart", "value" : "10", "level" : 8  },
    {"type" : "heart", "value" : "J",  "level" : 9  },
    {"type" : "heart", "value" : "Q",  "level" : 10 },
    {"type" : "heart", "value" : "K",  "level" : 11 },
    {"type" : "heart", "value" : "A",  "level" : 12 },
    {"type" : "heart", "value" : "2",  "level" : 15 },
    {"type" : "spade", "value" : "3",  "level" : 1  },
    {"type" : "spade", "value" : "4",  "level" : 2  },
    {"type" : "spade", "value" : "5",  "level" : 3  },
    {"type" : "spade", "value" : "6",  "level" : 4  },
    {"type" : "spade", "value" : "7",  "level" : 5  },
    {"type" : "spade", "value" : "8",  "level" : 6  },
    {"type" : "spade", "value" : "9",  "level" : 7  },
    {"type" : "spade", "value" : "10", "level" : 8  },
    {"type" : "spade", "value" : "J",  "level" : 9  },
    {"type" : "spade", "value" : "Q",  "level" : 10 },
    {"type" : "spade", "value" : "K",  "level" : 11 },
    {"type" : "spade", "value" : "A",  "level" : 12 },
    {"type" : "spade", "value" : "2",  "level" : 15 },
    {"type" : "block", "value" : "3",  "level" : 1  },
    {"type" : "block", "value" : "4",  "level" : 2  },
    {"type" : "block", "value" : "5",  "level" : 3  },
    {"type" : "block", "value" : "6",  "level" : 4  },
    {"type" : "block", "value" : "7",  "level" : 5  },
    {"type" : "block", "value" : "8",  "level" : 6  },
    {"type" : "block", "value" : "9",  "level" : 7  },
    {"type" : "block", "value" : "10", "level" : 8  },
    {"type" : "block", "value" : "J",  "level" : 9  },
    {"type" : "block", "value" : "Q",  "level" : 10 },
    {"type" : "block", "value" : "K",  "level" : 11 },
    {"type" : "block", "value" : "A",  "level" : 12 },
    {"type" : "block", "value" : "2",  "level" : 15 },
    {"type" : "clubs", "value" : "3",  "level" : 1  },
    {"type" : "clubs", "value" : "4",  "level" : 2  },
    {"type" : "clubs", "value" : "5",  "level" : 3  },
    {"type" : "clubs", "value" : "6",  "level" : 4  },
    {"type" : "clubs", "value" : "7",  "level" : 5  },
    {"type" : "clubs", "value" : "8",  "level" : 6  },
    {"type" : "clubs", "value" : "9",  "level" : 7  },
    {"type" : "clubs", "value" : "10", "level" : 8  },
    {"type" : "clubs", "value" : "J",  "level" : 9  },
    {"type" : "clubs", "value" : "Q",  "level" : 10 },
    {"type" : "clubs", "value" : "K",  "level" : 11 },
    {"type" : "clubs", "value" : "A",  "level" : 12 },
    {"type" : "clubs", "value" : "2",  "level" : 15 },
    {"type" : "king",  "value" : "L",  "level" : 20 },
    {"type" : "king",  "value" : "B",  "level" : 30 }
];

var pokertype = [
    {"type":"one","permission":0},//单
    {"type":"two","permission":0},//对    
    {"type":"twomore","permission":0}//连对
    {"type":"three","permission":0},//三带一0,1
    {"type":"threetmore","permission":0},//飞机0,1
    {"type":"fourmore","permission":0},//四带二，0，1，2
    {"type":"list","permission":0},//顺子
    {"type":"four","permission":1},//顺子
    {"type":"kingtwo","permission":2},//王炸
]
