<?php 

namespace Tareq\Xcurd;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
 class Xform
 {

  /**
  *@author Tareq Hossain
  *@copyright MIT
  *@link <xtrinsic96@gmail.com>
  *@return $mixed
  */

  public static $grid;
  public static $hasGrid = false;


  public static function createForm($modelName)
  {
    $model =static::parseModel($modelName);
    $model = new $model();

    $data = "";
    foreach($model->form_fields as $key=>$value){
      if(is_array($value)){

        if(in_array('model',array_keys($value))){
          $relModel = static::parseModel($value['model']);
          $data .= static::input('select',['name'=>$key],$relModel::all());
        }

        if(in_array('grid',array_keys($value))){
          $data .= static::grid($value['grid'])->input($value['type'],['name'=>$key]);
        }

      }else{
        $data .= static::input($value,['name'=>$key]);
      }
    }

    return $data;
  }


  public static function save($modelName,Request $request)
  {
    $model =  static::parseModel($modelName);
    $model = new $model();
      foreach ($request->except('_token') as $key => $value) {

            if($request->hasFile($key)){
              $model->{$key} = $request->file($key)->store(Str::plural(lcfirst($modelName)),'public');
            }else{
              $model->{$key} = $value;
            }
            
      }
      return $model->save();
  }

  public static function editForm($model)
  {
    $data = "";
    foreach($model->form_fields as $key=>$value){
      if(is_array($value)){

        if(in_array('model', array_keys($value))){
          $relModel = static::parseModel($value['model']);
          $data .= static::input('select',['name'=>$key,'selected'=>$model->{$key}],$relModel::all());
        }

        if(in_array('grid',array_keys($value))){
          $data .= static::grid($value['grid'])->input($value['type'],['name'=>$key],$model->{$key});
        }

      }else{
        $data .= static::input($value,['name'=>$key],$model->{$key});
      }
    }
    return $data;
  }

  public static function update($model,Request $request)
  {
      foreach ($request->except(['_token','_method']) as $key => $value) {
            if($request->hasFile($key)){
              $model->{$key} = $request->file($key)->store(Str::plural(lcfirst(class_basename($model))),'public');
            }else{
              $model->{$key} = $value;
            }
            
      }
      if($model->save()){
         return redirect()->route(lcfirst(class_basename($model)).'.index')->with('mgs',['Success',class_basename($model).' Updated Successfully!']);
      }else{
          return redirect()->back()->with('mgs',['Something going wrong']);
      }
  }


  public static function parseModel($modelName)
  {
    $mn = '\App\\'.$modelName;
    return $mn;
  }
  public static function grid($grid)
  {
    static::$grid = $grid;
    static::$hasGrid = true;
    return new static;
  }

   public static function open($action,$arr=null,$entype=false)
   {
      if(is_array($action)):
        $data =  '<form action="'.route($action[0],$action[1]).'" method="post"';
        else:
        $data =  '<form action="'.route($action).'" method="post"';
      endif;
      foreach($arr as $key=>$value):
          $data .= ''.$key.' = "'.$value.'"';
      endforeach;
      if($entype)
          $data .= 'enctype="multipart/form-data"';
      $data .= ">";

      $data .= csrf_field();
      return $data;

   }


   public static function input($type,$arr=null,$value=null)
   {
     $data = "";

     if(!in_array('label',array_keys($arr)))
     $arr['label'] = ucfirst($arr['name']);
     $data .= '<div class="form-group">';
       if($type != 'submit')
         $data .= '<label for="'.$arr['name'].'">'.$arr['label'].':</label>';

      if($type=='textarea'):
          $data .= '<textarea name="'.$arr['name'].'" class="form-control" id="'.$arr['name'].'" placeholder="'.$arr['label'].'">'.$value.'</textarea>';
      elseif($type=='select'):
          $data .= '<select name="'.$arr['name'].'" class="form-control" id="'.$arr['name'].'" >';
          $data .= '<option >--select--</option>';
          if ($value instanceof \Illuminate\Database\Eloquent\Collection) {

            if(in_array('selected', array_keys($arr))){
              foreach($value as $val){
                if($val->id == $arr['selected']){
                    $data .= '<option value="'.$val->id.'" selected>'.$val->name.'</option>';
                }else{
                  $data .= '<option value="'.$val->id.'">'.$val->name.'</option>';
                }
              }
            }else{
              foreach($value as $val){
                $data .= '<option value="'.$val->id.'">'.$val->name.'</option>';
              }
            }
          }else{
            foreach($value as $key=>$val){
              $data .= '<option value="'.$key.'">'.$val.'</option>';
            }
          }
          $data .= '</select>';
      elseif($type == 'submit'):
        if(in_array('icon', array_keys($arr))){
          $data .= '<button type="'.$type.'" name="'.$arr['name'].'" class="'.$arr['class'].'" id="'.$arr['name'].'">';
          $data .= '<i class="fa fa-'.$arr['icon'].'"></i>';
          $data .= $value;
          $data .= '</button>';
        }else{
          $data .= '<input type="'.$type.'" name="'.$arr['name'].'" class="'.$arr['class'].'" id="'.$arr['name'].'" value="'.$value.'" >';
        }
        
      else:
        if($type=="file" && $value!= null){
          $data .= static::updateImage($arr['name'],$value,$arr['label']);
        }else{

          $data .= '<input type="'.$type.'" name="'.$arr['name'].'" class="form-control" id="'.$arr['name'].'"  value="'.$value.'" placeholder="'.$arr['label'].'">';
        }

      endif;
      $data .= " </div>";

      if(static::$hasGrid){
        return static::withGrid(static::$grid,$data);
      }else{
        return $data;
      }
      
   }

   public static function close()
   {
      return "</form>";
   }

   public static function withGrid($grid, $data)
   {
      $result = "<div class='".$grid."'>";
      $result .= $data;
      $result .= "</div>";

      return $result;
   }


   public static function updateImage($name,$value,$label)
   {
          $data = '
            <div class="col-md-6 col-xs-6">
              <div class="text-center">
                <label for="image">'.$label.':</label>
                <input type="file" class="form-control" name="'.$name.'" onchange="readURL(this);">
                <div>
                  <img src="'.asset("no-image.png").'" id="imgPreview" alt="" style="height:100px; width:100px;">
                </div>
              </div>

            </div>
            <div class="col-md-6 col-xs-6" style="border-left: 2px solid blue;">
              <div class="text-center">
                  <label for="image">Current:</label><br>
                  <img src="'.asset($value).'" alt="" style="width:100px; height:100px; border: 1px solid black;">

              </div>
            </div>';

            return $data;
   }


 }
