<?php 

namespace Tareq\Xcurd;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;
use Illuminate\Support\Route;

class Xtable
{

	/**
	*@author Tareq Hossain 
	*@copyright MIT
	*@link <xtrinsic96@gmail.com>
	*@return $mixed
	*/
  
	public static $grid = null;
	public $row;
	public $pagination;
	public $filter;
	public $data;
	public static $model;
	public static $modelName;
	public static $routeName;
	public static $modelFullPath;

	public function __construct()
	{
		
	}

	public static function grid($grid)
	{
		static::$grid = $grid;
		return new static;
	}

	public static function model($model)
	{
		$data = "";
		static::$modelName = strtolower($model);
		static::$model = static::parseModel($model);
		$md = new static::$model();
		if(static::$grid != null){
			$data .= '<div class="'.static::$grid.'">';
		}
		$data .= static::tableCol($md->table_fields);
		$data .= static::tableBody($md->table_fields);
		$data .= '</table>';
		if(static::$grid != null){
			$data .= '</div>';
		}
		return $data;
	}


	public static function parseModel($modelName)
	{
		$mn = '\App\\'.$modelName;
	    return $mn;
	}

	public static function parseRoute($action, $param =null)
	{
   		$arr = get_class_vars(static::$model);
   		$routeName = "";
		if(Arr::has($arr,'routeNamePrefix')){
			$routeName .= Arr::get($arr,'routeNamePrefix').".";
		}
		$routeName.= static::$modelName.".".$action;

		return Route::has($routeName)?$param != null?route($routeName,$param):route($routeName):null;
		
	}





	public static function tableCol($arr)
	{
		$data = '';
		if(static::parseRoute('create') != null){
		$data .= '<a href="'.static::parseRoute('create').'" class="btn btn-success pull-right">+ New</a>';
		}
		$data .= '<table class="table table-bordered table-condensed table-striped">';
		$data .= '<thead><tr>';
		$data .= '<th>#</th>';
		foreach ($arr as $key => $value) {
			if(Str::contains($value,['_id'])){
				$name = explode('_',$value)[0];
				$data .= '<th>'.ucfirst($name).'</th>';
			}else{
				$data .= '<th>'.ucfirst($value).'</th>';
			}
		}
		$data .= '<th>Action</th>';
		$data .= '</tr><thead>';

		return $data;
	}

	public static function tableBody($arr)
	{
		$results =static::$model::all();
		$data = '<tbody>';
		$count = 1;
		foreach($results as $result){
			$data .= '<tr>';
			$data .= '<td>'.$count++.'</td>';
			foreach($arr as $field){
				if(Str::contains($result->{$field},['.jpg','.jpeg','.png','.svg']))
				{
					$data.= '<td><img src="'.asset($result->{$field}).'" style="height:40px;width:40px;" /></td>';
				}elseif(Str::contains($result->{$field},['_id'])){
					dd('found');
					$name = $result->explode('_',$result->{$field})[0]->name;
					dd($name);
					$data .= '<td>'.$name.'</td>';
				}else{
					$data .= '<td>'.$result->{$field}.'</td>';
				}
			}	
			$data .= '<td>';
			if(static::parseRoute('show') != null){
				$data .= '<a href="'.static::parseRoute('show',$result).'" class="btn btn-info" style="border-radius:0px;"><i class="fa fa-eye"></i> View</a>';
			}
			if(static::parseRoute('edit') != null){
				$data .= '<a href="'.static::parseRoute('edit',$result).'" class="btn btn-warning" style="border-radius:0px;"><i class="fa fa-edit"></i> Edit</a>';
			}
			if( static::parseRoute('destroy') != null){
			$data .= '<a href="'.static::parseRoute('destroy',$result).'" class="btn btn-danger" onclick="event.preventDefault();document.getElementById('."'delete-".$result->id."'".').submit();" style="border-radius:0px;">x</a>';
			$data .='<form id="delete-'.$result->id.'" action="'.static::parseRoute('destroy',$result).'" method="post">';
			$data .= csrf_field();
			$data .= method_field('DELETE');
			$data .= '</form>';

			}

			$data .= '</td>';
			
			$data .= '</tr>';	
		}
		$data .= '</tbody>';

		return $data;
	}

	public function showSearchField()
	{
		$data  ="<form action='".$action."' method='post' >";
		$data .= csrf_field();
		$data .='<input type="text" name="search" class="form-control">';
		$data .='<input type="submit" value="Serach" class="btn btn-success>"';
		$data .="</form>";
	}


	public function pagination()
	{

	}

	public function lazyLoading()
	{

	}

	public function search($action)
	{

	}

	public function export()
	{
		//export xlxs
		//export pdf
		//export csv

	}

	public function import()
	{
		//import xlxs
		//import csv
	}

	public function filter($arr=null)
	{

	}

	public function tableWithInput()
	{

	}



}