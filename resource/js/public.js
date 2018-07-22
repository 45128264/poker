
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
    {"type" : "heart", "value" : "2",  "level" : 13 },
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

var pokertype = {
    "one":{"level":0},//单
    "two":{"level":0},//对    
    "twomore":{"level":0},//连对
    "three":{"level":0},//三带一
    "threetmore":{"level":0},//飞机
    "fourmore":{"level":0},//四带二，1，2
    "list":{"level":0},//顺子
    "four":{"level":1},//炸弹
    "kingtwo":{"level":2},//王炸
};
var message = [ 
	{'code':"1001","message":"不能这样出牌"},
	{'code':"1002","message":"您出的牌型不对"},
	{'code':"1003","message":"出牌超时"},
	{'code':"1004","message":"您出的牌太小"},
	{'code':"1005","message":"请选择要出的牌"},
	{'code':"200","message":"出牌成功"}
];
var operation = [
        {"name":"叫地主","func":"callLand","value":0},
        {"name":"抢地主","func":"callLand","value":1},
        {"name":"不叫","func":"callLand","value":2},
        {"name":"准备","func":"goReady"},
        {"name":"取消","func":"goReady"},
        {"name":"退出房间","func":"empty"},
        {"name":"不要","func":"empty"},
        {"name":"出牌","func":"putPoker"},
        {"name":"提示","func":"empty"}
];
var check = {
	type:function(data,top){
		//从小到大排序,数组反转
		// var list = check.getlist(data);
		var list = data;
		console.log(list);
		var arr = (check.repeat(list)).sort(check.sortKey);
		var current ={};
		var isKing = [20,30]		
		current.data = list;
		if(arr.length==0){
			return message[4];
		}
		switch(arr[0].num){
			case 1:
				if(list.length == 1){
					current.type = "one";
					current.level = arr[0].level;
					current.other = "";
					break;
				}else if(isKing.indexOf(list[0].level) != -1 && isKing.indexOf(list[1].level) != -1 &&  list.length == 2){
					current.type = "kingtwo";
					break;
				}else if(list.length > 4 ){
					// console.log(arr);
					var sarr = arr.sort(check.sort);
					console.log(sarr);
					var drr = check.filter(sarr);
					// console.log(drr);
					if(drr.length == sarr.length){
						current.type = "list";
						current.len = sarr.length;
						break;
					}
				}
				// console.log(current);
				return message[0];
				break;
			case 2:
				if(arr.length == 1){
					current.type = "two";
					current.level = arr[0].level;
					break;
				}else if(arr.length >2 ){
					//以arr降序排列，进行顺子筛选和单牌数量筛选
					var sarr = check.filter(arr.sort(check.sort)).filter(function(x){ return x.num == 2});
					if(sarr.length == arr.length){
						current.type = "twomore";
						current.len = arr.length;
						break;
					}
				}
				return message[0];
				break;
			case 3:
				console.log(arr);
				if(arr.length == 1){
					current.type = "three";
					current.level = list[0].level;
					break;
				}else   if(arr.length >1){
					    var threeArr = arr.filter(function(x){ return x.num==3;});

					    var onenum = check.getCount(arr,"num")-threeArr.length*3;;
					    if(onenum==0 && threeArr.length > 1){
					    	current.type = "threemore";
					    	current.len = threeArr.length;
							break;
					    }else if(onenum == 1 && threeArr.length == 1){
					    	current.type = "three";
							current.level = arr[0].level;
							current.other = 1;
							break;
					    }else if(onenum == threeArr.length){
					      	var darr = check.filter(threeArr.sort(check.sort));
							if(darr.length == threeArr.length){
								current.type = "threemore";
								current.len = threeArr.length
								break;
							}
					    }
				}
				return message[0];
				break;
			case 4:
				if(arr.length == 1){
					current.type = "four";
					current.level = list[0].level;
					break;
				}else if(arr.length>1){
					var fourArr = arr.filter(function(x){ return x.num==4;});
				    var onenum = check.getCount(arr,"num")-fourArr.length*4;;
				    if(onenum==0 && fourArr.length > 1){
				    	current.type = "fourmore";
						break;
				    }else if(onenum == fourArr.length || onenum/fourArr.length==2){
				      	var darr = check.filter(fourArr.sort(check.sort));
						if(darr.length == fourArr.length){
							current.type = "fourmore";
							current.len = fourArr.length;
							break;
						}
				    }else {
				    	var fournum = check.num(arr,4); 
				    	var threenum = check.num(arr,3); 
				    	var othernum = check.num(arr,[3,4]); 
				    	console.log(threenum,othernum);
				    	if(threenum == othernum){
				    		current.type = "threemore";
				    		current.len = threenum+fournum;
				    		break;
				    	}
				    }
				}	
				return message[0];
				break;
		}
		return current;
	},
	getlist:function(arr){
		var tmp = [];
		for(var i=0;i<arr.length;i++){
			tmp.push(pokerdata[arr[i]]);
		}
		return tmp;
	},
	num:function(data,item){
		var num=0;
		if(typeof(item)=="object"){
			data.forEach(function(val){
				if(item.indexOf(val.num) == -1){
					num += val.num;
				}
			})
		}else {
			data.forEach(function(val){
				if(val.num == item){
					num++;
				}
			})
		}
		return num;
	},
	getCount:function(data,key){
		let num=0;
		data.forEach(function(item){
			num += item[key];
		})
		return num;
	},
	repeat:function(data){
		var list = [];
		var arr = [];
		data.forEach(function(item){
			var key = arr.indexOf(item.level);
			if(key == -1){
				var obj = {};
				obj.level=item.level;
				// console.log(item.level);
				obj.num=1;				
				arr.push(item.level);
				list.push(obj);
			}else{
				list[key].num++;
			}
		})
		return list;
	},
	sort:function(x,y){
		return x.level < y.level?1:-1;
	},
	filter:function(tmp){
		var list = [];
		list.push(tmp[0]);
		for(var i=0;i<tmp.length-1;i++){
			if(tmp[i].level-tmp[i+1].level==1){
				list.push(tmp[i+1]);
			}else{
				return list;
			}
		}
		return list;
	},
	sortKey:function(x,y){
		return x.num < y.num?1:-1;
	}

}
