# 一、说明
在通过项目之后，对使用频率高的函数进行封装。便于开发使用。
包含以下函数，
## 1.获取uuid
## 2.常用的日期操作函数，获取日期，获取日期差，将日期时间戳转成日期，获取星期，根据日期计算日期，根据日期获取时间段（如早晨中午，下午等）
## 3.随机函数：随机数获取，随机汉字获取，随机颜色，随机渐变色
## 4.计算 hashCode值
## 5.http请求判断
## 6.前置补位
# 二、函数说明
## `使用前准备工作，使用前请将该文件在main.js中引入并挂载`
```javascript
    /**
     * 
    在 import Vue from 'vue'
       import App from './App'
    之后
     */
    import orangeUtil from './utl/orange-util.js'; //orange-util.js 所在文件目录
    Vue.use(orangeUtil);
```
* 准备好之后可以尽情的使用以下方法了
--------------------------------------------------
## 1.`uuid` 获取uuid
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|len|Number|可为空|uuid长度;取值范围为（2-36)|
|binary|Number|可为空|参数进制，如16进制，2进制，8进制等;取值范围为（2-36)|

### 示例
```javascript

//1.普通使用
this.$util.uuid(); //81869db0-cdb6-48a2-83dd-f9c7d7132aa3

//2.限制长度
this.$util.uuid(32);//89dc93f8-f242-447f-9348-85f40f3a

//3.指定进制编码
this.$util.uuid(32,34);//segpemtu-jk11-49r2-atkd-ui0v87pf

```

## 2.`getNowD` 获取日期 yyyy-MM-dd
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|addDay|Number|可为空|要添加的天数|
|date|Date|可为空|计算的日期，默认为当前日期|

### 示例
```javascript

//1.普通使用
this.$util.getNowD(); //"2020-06-28"

//2.在当前日期的基础上 +3天
this.$util.getNowD(3);//"2020-07-01"

//3.在当前日期的基础上 -10天
this.$util.getNowD(-10);//"2020-06-18"

//4.在指定的日期的基础上计算添加天数后的日期
this.$util.getNowD(3,new Date('2020-6-30'));//"2020-07-03"

```

## 3.`getNowT` 获取时间  HH:mm:ss
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|addMillisecond|Number|可为空|要在添加的毫毛（1分钟 = `1*60*1000`）|
|date|Date|可为空|计算的日期，不传时默认为当前日期|

### 示例
```javascript

//1.普通使用
this.$util.getNowT(); //"11:31:59"

//2.在当前时间的基础上 +30分钟
this.$util.getNowD(30*60*1000);//"11:36:57"

//3.在当前时间的基础上 -1小时
this.$util.getNowT(-1*60*60*1000);//"10:37:47"

//4.在指定的日期的基础上计算添加后的时间
this.$util.getNowT(1*60*1000,new Date('2020-6-30 11:20:22'));//"11:21:22"

```

## 4.`getNowDT` 获取时间 yyy-MM-dd HH:mm:ss
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|addDay|Number|可为空|要在添加的毫毛（1分钟 = `1*60*1000`）|
|date|Date|可为空|计算的日期，不传时默认为当前日期|

### 示例
```javascript

//1.普通使用
this.$util.getNowDT(); //"2020-06-28 11:39:29"

//2.在当前日期的基础上 +1天
this.$util.getNowDT(1);//"2020-06-29 11:39:58"

//3.在当前时间的基础上 -1天
this.$util.getNowDT(-1) //"2020-06-27 11:41:10"

//4.在指定的日期的基础上计算添加后的日期
this.$util.getNowDT(1,new Date('2020-6-30 11:20:22'));//"2020-07-01 11:20:22"

```

## 5.`getDayDiff` 获取两个日期之间相差天数 int
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|endDate|String\Date|不为空|结束日期|
|startDate|String\Date|不为空|开始日期|

### 示例
```javascript

//1.普通使用
this.$util.getDayDiff('2020-6-28 11:43:08','2020-5-28 11:43:20'); //30

//2.结束日期小于开始日期 ，返回负值
this.$util.getDayDiff('2020-6-28 11:43:08','2020-7-28 11:43:20');//-30

```


## 6.`getTimeSplit` 获取指定时间所属的时间称呼[凌晨,早上,上午,中午,下午,傍晚,晚上,夜里] String
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|dateTime|String\Date|可为空|要计算的日期[yyyy-MM-dd HH:mm:ss] ，默认为当前日期|

### 示例
```javascript

//1.普通使用 获取当前时间
this.$util.getTimeSplit(); //"上午"

//2.结束日期小于开始日期 ，返回负值
this.$util.getTimeSplit('2020-4-28 17:43:08');//"傍晚"

```

## 7.`getFormatTime` 将日期格式化为今天昨天，今年明年等格式 String
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|dateTime|String\Date|不为空|要计算的日期[yyyy-MM-dd HH:mm:ss] ，默认为当前日期|

*`注意`入参请确保格式必须为 yyyy-MM-dd HH:mm:ss ；否则会出现不准确情况
### 示例
```javascript

//1.普通使用
this.$util.getFormatTime('2020-05-28 11:56:37'); //"今年 05-28 "

//2.
this.$util.getFormatTime('2019-05-28 11:56:37');//"去年 05-28 "

//3.
this.$util.getFormatTime('2019-06-28 11:56:37');//"今天 11:56"

//4.
this.$util.getFormatTime('2019-06-27 11:56:37');//"昨天 11:56"

//5.
this.$util.getFormatTime('2020-06-29 11:56:37');//"明天 11:56"

```

## 8.`getNowW` 获取一个日期对应的星期 String
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|addDay|Number|可为空|要在添加的天数|
|date|Date|可为空|计算的日期，不传时默认为当前日期|
*`注意`入参date请确保格式必须为 yyyy-MM-dd HH:mm:ss ；否则会出现不准确情况

### 示例
```javascript

//1.普通使用
this.$util.getNowW(); //"星期日"

//2.十天后是星期几
this.$util.getNowW(10);//"星期三"

//3.十天前是星期几
this.$util.getNowW(-10);//"星期四"

//4.指定日期后的 几天 是星期几
this.$util.getNowW(19,'2019-06-27');//"星期二"

//5,获取指定日期的星期几
this.$util.getNowW(0,'2022-06-27');//"星期一"

```

## 9.`getDateByNumber` 将数字串转换成日期格式的字符串 String
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|date|Date|不可为空|要转换的日期数字格式|

### 示例
```javascript

//1.普通使用 将1593317580211转成日期
this.$util.getDateByNumber ('1593317580211');//"2020-06-28 12:13:00"
```


## 10.`randomNum` 得到随机数字(包前不包后) String
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|min|Number|不为空|随机字符产生的最小值 (包含最小值)|
|max|Number|不为空|随机字符产生的最大值 (不包含最大值)|
|type|Number|可为空|产生的随机方式【`0` 只保留整数(默认值)	`1`	向上取整(只对小数起作用)	`2`	向下取整(只对小数起作用)	`3`	四舍五入(只对小数起作用)】|

### 示例
```javascript

//1.普通使用
this.$util.randomNum(0,10);//"2"

//2.使用type
this.$util.randomNum(6.01,60.10,1);//"39"
```



## 11.`randomChinese` 随机汉字字符 String
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|start|Number|可为空|开始字符数字 (默认值： 0x4e0=1248)|
|end|Number|可为空|结束字符数字 (默认值 0x9fa5=40869)|

### 示例
```javascript

//1.普通使用 
this.$util.randomChinese();//"嵇"

//2.设置区间
this.$util.randomChinese(2048,40000);//"鋖"

//2.设置区间 生成一段常用表情字符的区间
//具体字符可以参考 http://test.orange-info.cn/tools/3/index.html
this.$util.randomChinese(9994,10074);//"✭" "✍" "✱" "✚"


```




## 12.`randomColor` 生成随机颜色 String
### 参数
	无

### 示例
```javascript

//1.普通使用 
this.$util.randomColor();//"#37c7a7"


```


## 13.`randomColorLinearGradient` 生成渐变颜色 Object{color1:'',color2:''}
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|isAuto|Boolean|可为空|是否内置渐变色生成 （默认值为false）|

### 示例
```javascript

//1.普通使用 
this.$util.randomColorLinearGradient();//{color1: "rgb(253, 187, 45)", color2: "rgb(58, 28, 113)"}
//2.使用给定的颜色进行生成
this.$util.randomColorLinearGradient(true);//{color1: "#a8fe05", color2: "#97f35e"}

```
* 最后可以通过css 拼接成 渐变颜色  渐变语法参考 *
[css渐变语法参考](https://www.runoob.com/css3/css3-gradients.html)

## 14.`isHttpUrl` 判断一个字符串是否是请求连接地址 Boolean
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|url|String|不为空|需要判断的字符是否是http请求|

### 示例
```javascript

//1.普通使用 
this.$util.isHttpUrl('https://www.jianshu.com/p/96ecaa2cc989');//true
//2.使用给定的颜色进行生成
this.$util.isHttpUrl('https://www');//false

```

## 15.`hashCode` 根据字符串计算 Number
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|str|String|要计算的字符串|

### 示例
```javascript

//1.普通使用 计算字符串
this.$util.hashCode('https://www.jianshu.com/p/96ecaa2cc989');//582844592
//2.使计算其他类型
this.$util.hashCode([1,2,3,42,5]);//-528217203

```


## 16.`padLeft` 对数字或字符进行前置补位 String
### 参数
|参数名称|类型|是否可为空|说明|
|:---|:---|:---|:---|
|str|String|不为空|需要部位的字符串|
|length|Number|不为空|最终字符串长度|
|replaceChar|String|可为空|补位字符 ，默认为0|

### 示例
```javascript

//1.普通使用 计算字符串
this.$util.padLeft('2',2);//02

//2.使计算其他类型
this.$util.padLeft('2',2,'x');//"x2"

```

##


