/**
 *
 * describe: 常用函数工具类
 * author:jpw
 * Date:2019/12/3
 * Time:9:56
 *
 * */

//获取随机id
const uuid = function(len, binary) {
	len = !len ? 36 : len;
	binary = !binary ? 16 : binary;
	return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
		var r = Math.random() * binary | 0,
			v = c == 'x' ? r : (r & 0x3 | 0x8);
		return v.toString(binary);
	}).substring(0, len)
};

//获取两个日期之间相差天数
const getDayDiff = function(endDate, startDate) {
	startDate = startDate || getNowD();
	startDate = typeof startDate == "string" ? new Date(startDate) : startDate;
	endDate = typeof endDate == "string" ? new Date(endDate) : endDate;
	var s1 = startDate.getTime(),
		s2 = endDate.getTime();
	var total = (s2 - s1) / (1000 * 24 * 60 * 60);
	var day = parseInt(total); //计算整数天数
	return day;
}
/**
 * 得到当前日期  年月日
 */
const getNowD = function(addDay, date) {
	date = date || new Date();
	date = typeof date == 'string' ? new Date(date) : date;
	addDay = addDay || 0;
	date.setDate(date.getDate() + addDay);
	return date.getFullYear() + "-" + padLeft((date.getMonth() + 1), 2) + "-" + padLeft(date.getDate(), 2);
}

/**
 * 得到当前日期  年月日 时分秒
 */
const getNowDT = function(addDay, date) {
	date = date || new Date();
	addDay = addDay || 0;
	date.setDate(date.getDate() + addDay);
	return date.getFullYear() + "-" + padLeft((date.getMonth() + 1), 2) + "-" + padLeft(date.getDate(), 2) +
		" " + padLeft(date.getHours(), 2) + ":" + padLeft(date.getMinutes(), 2) + ":" + padLeft(date.getSeconds(), 2);
}
/**
 * 获取一个日期对应的星期
 * @param {Object} addDay 添加的天数,可为负数
 * @param {Object} date
 */
const getNowW = function(addDay, date) {
	var date = getNowD(addDay, date);
	var d = new Date(date);
	var str = ["星期日", "星期一", "星期二", "星期三", "星期四", "星期五", "星期六"];
	return str[d.getDay()];
}

/**
 * 得到当前时间 或者在当前时间的基础上 加上一定的时间
 * @param addMinutes  1分钟 = 1*60*1000
 * @param date
 * @return {string}
 */
const getNowT = function(addMillisecond, date) {
	date = date || new Date();
	addMinutes = addMinutes || 0;
	date.setTime(date.getTime() + addMinutes);
	return padLeft(date.getHours(), 2) + ":" + padLeft(date.getMinutes(), 2) + ":" + padLeft(date.getSeconds(), 2);
}

/**
 * 将数字串转换成日期格式的字符串
 * @param number
 * @return {*}
 */
const getDateByNumber = function(number) {
	if (!isNaN(number)) {
		number = parseInt(number);
		return getNowDT(0, new Date(number));
	}
	return number;
}

/**
 * 得到随机数字 (包前不包后)
 * @param {Object} min 最小值
 * @param {Object} max 最大值
 * @param {Object} type 类型
 * 						0		  只保留整数(默认值)
 * 						1 		  向上取整
 * 						2 		  向下取整
 * 						3 		  四舍五入
 * 
 */
const randomNum = function(min, max, type) {
	if ((!min&&min!=0) ||(!max&&min!=0)) {
		throw "最小值或最大值不能为空";
	}

	var random = Math.random() * (max - min) + min;
	switch (type) {
		case 1:
			return Math.ceil(random);
		case 2:
			return Math.floor(random);
		case 3:
			return Math.round(random);
		default:
			return parseInt(random);
	}
}

/**
 * 随机汉字 字符
 * @param {Number} start 开始字符数字 0x4e00
 * @param {Number} end 结束字符数字 0x9fa5
 */
const randomChinese = function(start, end) {
	start = start || 0x4E00;
	end = end || 0x9FA5;
	var ran = randomNum(start, end);
	return String.fromCharCode(ran)
}


//将日期格式化为今天昨天，今年明年等格式
/**
 * @param {Object} time 时间
 * 							
 */
const getFormatTime = function(dateTime) {
	var now = getNowD();
	if (dateTime.indexOf(now) >= 0) {
		return dateTime.substr(0, 16).replace(now, '今天')
	}
	var yesterday = getNowD(-1);
	if (dateTime.indexOf(yesterday) >= 0) {
		return dateTime.substr(0, 16).replace(yesterday, '昨天')
	}

	var tomorrow = getNowD(1);
	if (dateTime.indexOf(tomorrow) >= 0) {
		return dateTime.substr(0, 16).replace(tomorrow, '明天')
	}

	var year = getNowD().substr(0, 4);
	if (dateTime.indexOf(year) >= 0) {
		dateTime = dateTime.substr(0, 10).replace(year + "-", '今年 ')
	}


	var lastYear = parseInt(year) - 1;
	if (dateTime.indexOf(lastYear) >= 0) {
		dateTime = dateTime.substr(0, 10).replace(lastYear + "-", '去年 ')
	}

	if (dateTime.indexOf('去年') >= 0 || dateTime.indexOf('今年') >= 0 || time.indexOf('昨天') >= 0 || time.indexOf('今天') >= 0) {
		return dateTime;
	}
	return dateTime.substr(0, 16);
}

//对数字进行前置补位
const padLeft = function(str, length,replaceChar) {
	return ("0000000000000000".replace(/[0]/g,replaceChar||0) + str).substr(-length);
}
/**-----------样式操作 */

/**
 * 判断一个字符串是否是请求连接地址
 * @param {Object} url
 */
const isHttpUrl = function(url) {
	var regExp = /http(s)?:\/\/([\w-]+\.)+[\w-]+(\/[\w- .\/?%&=]*)?/;
	return !(!regExp.test(url))
}

/**
 * 根据字符串计算hashCode值
 * @param {Object} str
 */
const hashCode = function(str) {
	str=typeof str!='string'?JSON.stringify(str):str;
	var hash = 0,
		i, chr, len;
	if (str.length === 0) return hash;
	for (i = 0, len = str.length; i < len; i++) {
		chr = str.charCodeAt(i);
		hash = ((hash << 5) - hash) + chr;
		hash |= 0; // Convert to 32bit integer
	}
	return hash;
}

/**
 * 生成随机颜色
 */
const randomColor = function() {
	var color = "#";
	for (var i = 0; i < 6; i++) {
		color += (Math.random() * 16 | 0).toString(16);
	}
	return color;
}

/**
 * 生成随机渐变色
 */
const randomColorLinearGradient = function(isAuto) {
	var color = [{
			color1: 'rgb(253, 223, 70)',
			color2: 'rgb(23, 213, 209)'
		},
		{
			color1: 'rgb(28, 181, 224)',
			color2: 'rgb(0, 8, 81)'
		},
		{
			color1: 'rgb(0, 201, 255)',
			color2: '  rgb(146, 254, 157)'
		},
		{
			color1: 'rgb(252, 70, 107)',
			color2: 'rgb(63, 94, 251)'
		},
		{
			color1: 'rgb(227, 255, 231)',
			color2: ' rgb(217, 231, 255) '
		},
		{
			color1: 'rgb(253, 187, 45)',
			color2: 'rgb(58, 28, 113)'
		},
		{
			color1: 'rgb(75, 108, 183)',
			color2: 'rgb(24, 40, 72)'
		},
		{
			color1: 'rgb(7, 0, 184)',
			color2: 'rgb(0, 255, 136)'
		},
		{
			color1: 'rgb(213, 51, 105)',
			color2: 'rgb(218, 174, 81)'
		},
		{
			color1: 'rgb(0, 210, 255)',
			color2: '  rgb(58, 71, 213)'
		}
	]
	if (isAuto) {
		return {
			color1: randomColor(),
			color2: randomColor()
		};
	}
	var random = randomNum(0, color.length);
	return color[random];
}


/**
 * 根据日期生成对应的时段
 */
const _hourSplitArray = [{
		hour: 6,
		text: '凌晨'
	},
	{
		hour: 9,
		text: '早上'
	},
	{
		hour: 12,
		text: '上午'
	},
	{
		hour: 14,
		text: '中午'
	},
	{
		hour: 17,
		text: '下午'
	},
	{
		hour: 19,
		text: '傍晚'
	},
	{
		hour: 22,
		text: '晚上'
	},
	{
		hour: 24,
		text: '夜里'
	}
]
const getTimeSplit = function(dateTime) {
	dateTime = dateTime || new Date();
	dateTime = typeof dateTime == 'string' ? new Date(dateTime) : dateTime;
	var hour = dateTime.getHours()
	for (let i = 0; i < _hourSplitArray.length; i++) {
		var _h = _hourSplitArray[i]['hour'];
		if (hour < _h) {
			return _hourSplitArray[i]['text']
		}
	}
	return txet = ''
}



const install = (Vue) => {
	Vue.prototype.$util = {
		uuid,
		//日期
		getNowD,
		getNowT,
		getNowDT,
		getDayDiff,
		getTimeSplit,
		getFormatTime,
		getNowW,
		getDateByNumber,

		//随机函数
		randomNum,
		randomChinese,
		randomColor,
		randomColorLinearGradient,

		//其他
		isHttpUrl,
		hashCode,
		padLeft,
	};
}

// 这里我们导出一个对象，内部有一个叫"install"的方法，给上面说的Vue.use调用
export default {
	install
}
