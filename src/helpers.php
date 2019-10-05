<?php 

  /**
  *@author Tareq Hossain 
  *@copyright MIT
  *@link <xtrinsic96@gmail.com>
  *@return $mixed
  */
  
if(!function_exists('isActive')){
	function isActive($name){
		return Route::currentRouteName()==$name?'active':null;
	}
}

if(!function_exists('setting')){
	function setting($name){
		//$setting = \App\Setting::where('name',$name)->first();
		//return $setting?$setting->data:null;
	}
}



