<?php
$rules=[
  " (",//for open functin paremeters
  "if(",//for if statements
  "){",//for ending function paremeters
  "function",//detecting functions
  "Jcss",//for variables
];
   //all rules for now

//input example
  $input='
  function bodyContent({{param,myColor,myFont}}){
  background:param;
  color:myColor;
  font-weight:bolder;
  font-size:param;
  font-family:myFont;
}

function go({{color , background}}){
color:red;
background:Red;
color:red;
Jcss.call(bodyContent(grey,green,arial));
}

body{
 background:Red; 
 Jcss.call(bodyContent(blue,red,cursive));
}';
$cssOutput=``;
//features and processes
interface JcssGet{//execute process1
  public function getJcssFunctions();
  public function getJcssVariables();
 }
interface JcssRules{//checking rulls
  public function checkAllErrors();
  public function getRules();//to avoid using too much of $ sincs Jcss also use it for variable naming
  public function JcssError();
}

trait JcssExecuteFunc{//object = excute functions
    public function getFunctionList(){
      if(method_exists($this,"getJcssFunctions"))   $functions=$this->getJcssFunctions();//will return array of all functions
  else $functions=[];
    //print_r($functions);
    //echo "<br><br>";
    $functionsList=[];
    $index=0;
    global $input;
    //echo $input;
      foreach($functions as $i){
        $index++;
        if(trim($i)==="")continue;
      $funcEndIndex=strpos($i,"){")+($index==count($functions)?0:1);
      $funcName=substr($i,0,$funcEndIndex);//get function name
     // echo $funcName;
      $func1=substr($i,$funcEndIndex+2);
      $funcBody=substr($func1,0,strpos($func1,"}"));//get function body
      $funcParameters=substr($funcName,strpos($funcName,"(")+3,strpos($funcName,"}"));
      $funcParameters=strpos($funcParameters,")") ? explode(")",$funcParameters)[0]:$funcParameters;
      $funcParameters=substr($funcParameters,0,strlen($funcParameters)-2);//paremeters goted
        array_push($functionsList,//saving functions to array
        array(
          "name"=>$funcName,
          "body"=>$funcBody,
          "paremeters"=>trim($funcParameters)!="" ? $funcParameters:null
        )//end array level2
      );//end array level1
      }//functions successfully goted
    //print_r($functionsList);
    

   //execute prmeters now
   $parametersListAll=[];
   //print_r($functionsList);
   $index=0;
foreach($functionsList as $func){
  $index++;
  if($func["paremeters"]===null)continue;

  $parametersList=$func["paremeters"];
  $parameters=(strpos($parametersList,",")) ? explode(",",$parametersList):[$parametersList];
  $functionsList[$index]["paremeters"]=$parameters;
  $functionsList[$index]["body"]=$func["body"];
  $functionsList[$index]["name"]=$func["name"];
  //parameters goted
  $this->executeFunCall($functionsList[$index]);
}//end executing paremeters
//print_r($parametersListAll);
//print_r($functionsList);
   }
  
   public function executeFunCall($func=array()){
     //find paremeter var with in function scope ;
  
     $funcName="Jcss.call(".trim(substr($func["name"],0,strpos($func["name"],"(")));
    //find functions calls
global $input;
     $funcCalls=explode($funcName,$input);
 
   $body=$func["body"];
   $bodyProcess=$body;
   echo "<br>before <br>".$bodyProcess."<br>";
   $newInput='';
   $index=0;
   foreach($funcCalls as $putFunc){//where function is called
   $index++;
  if($index===1)continue;
    $parameterGiven=substr($putFunc,1,strpos($putFunc,")")-1);
   $parameterGiven=strpos($parameterGiven,",") ? explode(",",$parameterGiven):$parameterGiven;

 $varIndex=0;//get index of parameter only for array prm uses
    foreach($func["paremeters"] as $var){//replace paremeter with function called given paremeter
    if(gettype($parameterGiven)==="string"){
    $bodyProcess=str_replace($var,$parameterGiven,$bodyProcess);
    }
    elseif(gettype($parameterGiven)==="array"){

          $bodyProcess=str_replace($var,$parameterGiven[$varIndex], $bodyProcess);
          $varIndex++;
      
        }
  }//paremeter execution ends here
  echo "<br>After<br>".$bodyProcess."<br>";
}//function execution ends here
  //extends to body  
   }
    
}

trait JcssExecuteVars{//object = execute var
}

/*
trait JcssExecutesAll{
  private $JcssErrors="";
  public function cssSuccess($cssString){
    $cssOutput.=$cssString;
  }

  public function JcssErrorSee($error){
$this->$JcssErrors.="\n\n<b>Error:</b>\n".$error;
  }
}
*/

class JcssExecProcess implements JcssGet,JcssRules{
use JcssExecuteFunc;
use JcssExecuteVars;
//use JcssExecuteAll;
public function getJcssFunctions(){
  global $input;
//  print $input;
  return explode("function",$input);
}
public function getJcssVariables(){
  global $input;
  return explode("Jcss ",$input);
}
//after executions checking rull
public function checkAllErrors(){
  if(isset($errorVar)){
    return $errorVar;
  }
}
public function getRules(){}//to avoid using too much of $ sincs Jcss also use it for variable naming
public function JcssError(){}
}

$Jcss=new JcssExecProcess();

$Jcss->getFunctionList();

//starts

