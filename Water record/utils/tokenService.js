export const setToken = (token) => {
	try{
		uni.setStorageSync('token',token)
	}catch(e){
		
	}
	
}
export const getToken = () => {
	try{
		return uni.getStorageSync('token')
	}catch(e){
		
	}
	
}
export const destoryToken = () => {
	try{
		uni.removeStorageSync('token')
	}catch(e){
		
	}
}