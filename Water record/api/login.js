import request from '@/utils/request.js'

// 登陆api 
export const login = (user) => {
	return request('/user/login', 'post', user, false)
}
// 验证码登陆
export const captcha_login = (user) => {
	return request('/user/captcha_login', 'post', user, false)
}
//发送邮件
export const sendMail = (email) => {
	return request(`/user/send_mail/${email}`, 'get', {}, false)
}
//获取用户信息
export const resGetUserInfo = () => {
	return request('/user/getUserInfo', 'get')
}