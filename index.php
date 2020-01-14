<?php

ini_set('log_errors','on');  //ログを取るか
ini_set('error_log','php.log');  //ログの出力ファイルを指定
session_start(); //セッション使う


$debug_flg = true;
//デバッグログ関数
function debug($str){
  global $debug_flg;
  if(!empty($debug_flg)){
    error_log('デバッグ：'.$str);
  }
}

// プレイヤー格納用
$humans = array();

// モンスター達格納用
$monsters = array();

//　プレイヤーカテゴリー
class Playertype{
  const BRAVE = 1;
  const WIZARD = 2;
}
// モンスターカテゴリー
class Monstercategory{
  const BASIC = 1;
  const MAGIC = 2;
  const FLY = 3;
}
// 性別クラス
class Sex{
  const MAN = 1;
  const WOMAN = 2;
  const OKAMA = 3;
}

// 抽象クラス（生き物クラス）
abstract class Creature{
  protected $name;
  protected $maxhp;
  protected $hp;
  protected $attackMin;
  protected $attackMax;
  protected $healMin;
  protected $healMax;
  abstract public function sayCry();
  public function setName($str){
    $this->name = $str;
  }
  public function getName(){
    return $this->name;
  }
  public function setHp($num){


    $this->hp = $num;
    
    if($num > $this->maxhp){
      $this->hp = $this->maxhp;
    }

  }
  public function upsetHp($num){

    $this->maxhp = $num;

  }
  public function getHp(){
    return $this->hp;
  }

  public function setAttackMin($num){
    $this->attackMin = $num;
  }

  public function getAttackMin(){
    return $this->attackMin;
  }

  public function setAttackMax($num){
    $this->attackMax = $num;
  }

  public function getAttackMax(){
    return $this->attackMax;
  }

  public function attack($targetObj){
    $attackPoint = mt_rand($this->attackMin, $this->attackMax);
    if(!mt_rand(0,9)){ //10分の1の確率でクリティカル
      $attackPoint = $attackPoint * 1.5;
      $attackPoint = (int)$attackPoint;
      History::set($this->getName().'のクリティカルヒット!!');
    }
    $targetObj->setHp($targetObj->getHp()-$attackPoint);
    History::set($attackPoint.'ポイントのダメージ！');
  }

  public function heal($targetObj){
    $healPoint = mt_rand($this->healMin, $this->healMax);
    $targetObj->setHp($targetObj->getHp()+$healPoint);
    History::set($healPoint.'ポイント回復！');
  }

  public function superheal($targetObj){
    $healPoint = $this->maxhp;
    $targetObj->setHp($targetObj->getHp()+$healPoint);
    error_log($healPoint);
    History::set($healPoint.'回復した！');
  }
  public function powerup($targetObj){
    $powerup = 20;
    $targetObj->setAttackMin($targetObj->getAttackMin()+$powerup);
    $targetObj->setAttackMax($targetObj->getAttackMax()+$powerup);
    History::set('攻撃力が20上がった！');
    
  }
  public function buildup($targetObj){
    $buildup = $this->maxhp;
    $targetObj->upsetHp($targetObj->getHp()+$buildup);
    // $targetObj->setHp($targetObj->getHp()*2);
    History::set('HPが'.$buildup.'上がった！');
  }

}
// 人クラス
class Human extends Creature{
  protected $playertype;
  protected $sex;
  public function __construct($name, $playertype, $sex, $maxhp, $hp, $attackMin, $attackMax, $healMin, $healMax) {
    $this->name = $name;
    $this->playertype = $playertype;
    $this->sex = $sex;
    $this->maxhp = $maxhp;
    $this->hp = $hp;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
    $this->healMin = $healMin;
    $this->healMax = $healMax;
  }
  public function setSex($num){
    $this->sex = $num;
  }
  public function getSex(){
    return $this->sex;
  }
  public function sayCry(){
    History::set($this->name.'が叫ぶ！');
    switch($this->sex){
      case Sex::MAN :
        History::set('ぐはぁっ！');
        break;
      case Sex::WOMAN :
        History::set('きゃっ！');
        break;
      case Sex::OKAMA :
        History::set('もっと！♡');
        break;
    }
  }
}
//魔法使いクラス
class Wizard extends Human{
  protected $mp;
  public function __construct($name, $playertype, $sex, $maxhp, $hp, $attackMin, $attackMax, $healMin, $healMax, $mp){
    parent::__construct($name, $playertype, $sex, $maxhp, $hp, $attackMin, $attackMax, $healMin, $healMax);
    $this->name = $name;
    $this->playertype = $playertype;
    $this->sex = $sex;
    $this->maxhp = $maxhp;
    $this->hp = $hp;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
    $this->healMin = $healMin;
    $this->healMax = $healMax;
    $this->mp = $mp;
  }
  public function attack($targetObj){
    if(!mt_rand(0,2)){ //3分の1の確率で魔法攻撃
      if($this->mp >= 10){ //MP判定
        if($targetObj['monscategory'] = 3){ //飛行モンスターへの魔法攻撃
          $attackPoint = mt_rand(mt_rand($this->attackMin, $this->attackMax) * 0.5, mt_rand($this->attackMin, $this->attackMax) * 2) * 1.5;
          $attackPoint = (int)$attackPoint;
          $targetObj->setHp( $targetObj->getHp() - $attackPoint);
          History::set($this->name.'の魔法攻撃!!');
          History::set('効果はばつぐんだ!!');
          History::set($attackPoint.'ポイントのダメージ！');
        }//魔法攻撃
        $attackPoint = mt_rand(mt_rand($this->attackMin, $this->attackMax) * 0.5, mt_rand($this->attackMin, $this->attackMax) * 2);
        $attackPoint = (int)$attackPoint;
        $targetObj->setHp( $targetObj->getHp() - $attackPoint);
        History::set($this->name.'の魔法攻撃!!');
        History::set($attackPoint.'ポイントのダメージ！');
      }else{
        History::set('MPが足りない！');
        parent::attack($targetObj);
      }
    }else{
      parent::attack($targetObj);
    }
  }

}
// モンスタークラス
class Monster extends Creature{
  // プロパティ
  protected $monscategory;
  protected $img;
  // コンストラクタ
  public function __construct($name, $monscategory, $hp, $img, $attackMin, $attackMax, $healMin, $healMax) {
    $this->name = $name;
    $this->monscategory = $monscategory;
    $this->hp = $hp;
    $this->img = $img;
    $this->attackMin = $attackMin;
    $this->attackMax = $attackMax;
  }
  public function setMonstercategory($num){
    $this->monscategory = $num;
  }
  public function getMonstercategory(){
    return $this->monscategory;
  }
  // ゲッター
  public function getImg(){
    return $this->img;
  }
  public function sayCry(){
    History::set($this->name.'が叫ぶ！');
    History::set('はうっ！');
  }
}
// 魔法を使えるモンスタークラス
class MagicMonster extends Monster{
  private $magicAttack;
  function __construct($name, $monscategory, $hp, $img, $attackMin, $attackMax, $healMin, $healMax, $magicAttack) {
    parent::__construct($name, $monscategory, $hp, $img, $attackMin, $attackMax, $healMin, $healMax);
    $this->magicAttack = $magicAttack;
  }
  public function getMagicAttack(){
    return $this->magicAttack;
  }
  public function attack($targetObj){
    if(!mt_rand(0,4)){ //5分の1の確率で魔法攻撃
      History::set($this->name.'の魔法攻撃!!');
      $targetObj->setHp( $targetObj->getHp() - $this->magicAttack );
      History::set($this->magicAttack.'ポイントのダメージ！');
    }else{
      parent::attack($targetObj);
    }
  }
}
//飛行モンスタークラス
class FlyingMonster extends Monster{
  function __construct($name, $monscategory, $hp, $img, $attackMin, $attackMax, $healMin, $healMax) {
    parent::__construct($name, $monscategory, $hp, $img, $attackMin, $attackMax, $healMin, $healMax);
  }
  public function attack($targetObj){
    if(!mt_rand(0,2)){ //3分の1の確率で飛行攻撃
      History::set($this->name.'の飛行攻撃!!');
      $attackPoint = mt_rand($this->attackMin, $this->attackMax) * 1.2;
      $attackPoint = (int)$attackPoint;
      $targetObj->setHp( $targetObj->getHp() - $attackPoint);
      History::set($attackPoint.'ポイントのダメージ！');
      parent::setHp(parent::getHp() - 20);
      History::set($this->name.'は20ポイントの反動を受けた！');
    }else{
      parent::attack($targetObj);
    }
  }
}
//神様クラス
class God{
  public static $name = '神様';
  // const name = 
  public static $img = 'img/god.png';
  public $superheal;
  public $buildup;
  public function __construct($superheal, $buildup){
    $this->superheal = $superheal;
    $this->buildup = $buildup;
  }
  public function getName(){
    return self::$name;
  }
  public function getImg(){
    return self::$img;
  }
  public function getSuperheal(){
    return $this->superheal;
  }
  public function heal($targetObj){
    $healPoint = $this->superheal;
    $targetObj->setHp($targetObj->getHp()+$healPoint);
    History::set('超回復した！');
  }
  public function Powerup($targetObj){
    $targetObj = $this->attackMin;
    History::set('攻撃力が上がった！');
    
  }
  public function Buildup($targetObj){
    History::set('HPが上がった！');
  }
}
interface HistoryInterface{
  public static function set($str);
  public static function clear();
}
// 履歴管理クラス（インスタンス化して複数に増殖させる必要性がないクラスなので、staticにする）
class History implements HistoryInterface{
  // public function set($str){
  public static function set($str){
    // セッションhistoryが作られてなければ作る
    if(empty($_SESSION['history'])) $_SESSION['history'] = '';
    // 文字列をセッションhistoryへ格納
    $_SESSION['history'] .= $str.'<br>';
  }
  // public function clear(){
  public static function clear(){
    unset($_SESSION['history']);
  }
}

// インスタンス生成
$humans[] = new Human('勇者', Playertype::BRAVE, Sex::MAN, 500, 500, 40, 120, 10, 100);
$humans[] = new Human('魔法使い', Playertype::WIZARD, Sex::WOMAN, 300, 500, 40, 120, 10, 100, mt_rand(50, 100));
$monsters[] = new Monster( 'フランケン', Monstercategory::BASIC, 100, 'img/monster01.png', 20, 40, 10, 100 );
$monsters[] = new MagicMonster( 'フランケンNEO', Monstercategory::MAGIC, 300, 'img/monster02.png', 20, 60, 10, 100, mt_rand(50, 100) );
$monsters[] = new Monster( 'ドラキュリー', Monstercategory::BASIC, 200, 'img/monster03.png', 30, 50, 10, 100 );
$monsters[] = new MagicMonster( 'ドラキュラ男爵', Monstercategory::MAGIC, 400, 'img/monster04.png', 50, 80, 10, 100, mt_rand(60, 120) );
$monsters[] = new FlyingMonster( 'アルカード', Monstercategory::FLY, 350, 'img/monster04.png', 40, 100, 10, 100 );
$monsters[] = new Monster( 'スカルフェイス', Monstercategory::BASIC, 150, 'img/monster05.png', 30, 60, 10, 100 );
$monsters[] = new FlyingMonster( 'フライングスケルトン', Monstercategory::FLY, 125, 'img/monster05.png', 20, 40, 10, 100 );
$monsters[] = new Monster( '毒ハンド', Monstercategory::BASIC, 100, 'img/monster06.png', 10, 30, 10, 100 );
$monsters[] = new Monster( '泥ハンド', Monstercategory::BASIC, 120, 'img/monster07.png', 20, 30, 10, 100 );
$monsters[] = new Monster( '血のハンド', Monstercategory::BASIC, 180, 'img/monster08.png', 30, 50, 10, 100 );
$god = new God( '神様', 'img/god.png');

function createCreature(){
  global $monsters;
  global $god;

  if(!mt_rand(0, 10)){

    unset($_SESSION['monster']);
    History::set($god->getName().'が現れた！');
    $_SESSION['god'] = $god;

  }else{

    $monster = $monsters[mt_rand(0, 9)];
    History::set($monster->getName().'が現れた！');
    $_SESSION['monster'] = $monster;

  }
  
}
function createHuman(){
  global $humans;
  $_SESSION['human'] = $humans;
}
function init(){
  History::clear();
  History::set('初期化します！');
  $_SESSION['knockDownCount'] = 0;
  $_SESSION['healcount'] = 0;
  createHuman();
  createCreature();
}
function gameOver(){
  $_SESSION = array();
}


//1.post送信されていた場合
if(!empty($_POST)){
  $startFlg = (!empty($_POST['start'])) ? true : false;
  $BstartFlg = (!empty($_POST['brave_start'])) ? true : false;
  $WstartFlg = (!empty($_POST['wizard_start'])) ? true : false;
  $attackFlg = (!empty($_POST['attack'])) ? true : false;
  $healFlg = (!empty($_POST['heal'])) ? true : false;
  $superhealFlg = (!empty($_POST['superheal'])) ? true : false;
  $powerupFlg = (!empty($_POST['powerup'])) ? true : false;
  $buildupFlg = (!empty($_POST['buildup'])) ? true : false;
  error_log('POSTされた！');
  
  // if($startFlg){
  //   init();
  if($BstartFlg){
    History::set('勇者でゲームスタート！');
    init();
    $_SESSION['playertype'] = 1;
  }else{
    // 攻撃するを押した場合
    if($attackFlg){
      
      // モンスターに攻撃を与える
      History::set($_SESSION['human']->getName().'の攻撃！');
      $_SESSION['human']->attack($_SESSION['monster']);
      $_SESSION['monster']->sayCry();
      
      // モンスターが攻撃をする
      History::set($_SESSION['monster']->getName().'の攻撃！');
      $_SESSION['monster']->attack($_SESSION['human']);
      $_SESSION['human']->sayCry();
      
      // 自分のhpが0以下になったらゲームオーバー
      if($_SESSION['human']->getHp() <= 0){
        gameOver();

      }else{
        // hpが0以下になったら、別のモンスターを出現させる
        if($_SESSION['monster']->getHp() <= 0){
          History::set($_SESSION['monster']->getName().'を倒した！');
          createCreature();
          $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
        }
      }
    
    }else if($healFlg){// 回復するを押した場合

      if($_SESSION['healcount'] >= 3){

        History::set('もう回復できません！');

      }else{
        // 回復する
        History::set($_SESSION['human']->getName().'の回復！');
        $_SESSION['human']->heal($_SESSION['human']);
        $_SESSION['healcount'] += 1;
      }

      // モンスターが攻撃をする
      History::set($_SESSION['monster']->getName().'の攻撃！');
      $_SESSION['monster']->attack($_SESSION['human']);
      $_SESSION['human']->sayCry();

      // 自分のhpが0以下になったらゲームオーバー
      if($_SESSION['human']->getHp() <= 0){
        gameOver();
      }

    }else if($superhealFlg){// 神：回復してもらうを押した場合

      History::set($_SESSION['god']->getName().'が回復してくれた！');
      $_SESSION['human']->superheal($_SESSION['human']);
      createCreature();

    }else if($powerupFlg){// 神：強くしてもらうを押した場合

        History::set($_SESSION['god']->getName().'が強くしてくれた！');
        $_SESSION['human']->powerup($_SESSION['human']);
        createCreature();

    }else if($buildupFlg){// 神：丈夫にしてもらうを押した場合

        History::set($_SESSION['god']->getName().'に丈夫にしてもらった！');
        $_SESSION['human']->buildup($_SESSION['human']);
        createCreature();

    }else{ //逃げるを押した場合
      History::set('逃げた！');
      createCreature();
    }
  }
  if($WstartFlg){
    History::set('魔法使いでゲームスタート！');
    init();
    $_SESSION['playertype'] = 2;
  }else{
    // 攻撃するを押した場合
    if($attackFlg){
      
      // モンスターに攻撃を与える
      History::set($_SESSION['human']->getName().'の攻撃！');
      $_SESSION['human']->attack($_SESSION['monster']);
      $_SESSION['monster']->sayCry();
      
      // モンスターが攻撃をする
      History::set($_SESSION['monster']->getName().'の攻撃！');
      $_SESSION['monster']->attack($_SESSION['human']);
      $_SESSION['human']->sayCry();
      
      // 自分のhpが0以下になったらゲームオーバー
      if($_SESSION['human']->getHp() <= 0){
        gameOver();

      }else{
        // hpが0以下になったら、別のモンスターを出現させる
        if($_SESSION['monster']->getHp() <= 0){
          History::set($_SESSION['monster']->getName().'を倒した！');
          createCreature();
          $_SESSION['knockDownCount'] = $_SESSION['knockDownCount']+1;
        }
      }
    
    }else if($healFlg){// 回復するを押した場合

      if($_SESSION['healcount'] >= 3){

        History::set('もう回復できません！');

      }else{
        // 回復する
        History::set($_SESSION['human']->getName().'の回復！');
        $_SESSION['human']->heal($_SESSION['human']);
        $_SESSION['healcount'] += 1;
      }

      // モンスターが攻撃をする
      History::set($_SESSION['monster']->getName().'の攻撃！');
      $_SESSION['monster']->attack($_SESSION['human']);
      $_SESSION['human']->sayCry();

      // 自分のhpが0以下になったらゲームオーバー
      if($_SESSION['human']->getHp() <= 0){
        gameOver();
      }

    }else if($superhealFlg){// 神：回復してもらうを押した場合

      History::set($_SESSION['god']->getName().'が回復してくれた！');
      $_SESSION['human']->superheal($_SESSION['human']);
      createCreature();

    }else if($powerupFlg){// 神：強くしてもらうを押した場合

        History::set($_SESSION['god']->getName().'が強くしてくれた！');
        $_SESSION['human']->powerup($_SESSION['human']);
        createCreature();

    }else if($buildupFlg){// 神：丈夫にしてもらうを押した場合

        History::set($_SESSION['god']->getName().'に丈夫にしてもらった！');
        $_SESSION['human']->buildup($_SESSION['human']);
        createCreature();

    }else{ //逃げるを押した場合
      History::set('逃げた！');
      createCreature();
    }
  }
  $_POST = array();//これは？
}

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <title>ホームページのタイトル</title>
    <style>
    	body{
	    	margin: 0 auto;
	    	padding: 150px;
	    	width: 25%;
	    	background: #fbfbfa;
        color: white;
    	}
    	h1{ color: white; font-size: 20px; text-align: center;}
      h2{ color: white; font-size: 16px; text-align: center;}
    	form{
	    	overflow: hidden;
    	}
    	input[type="text"]{
    		color: #545454;
	    	height: 60px;
	    	width: 100%;
	    	padding: 5px 10px;
	    	font-size: 16px;
	    	display: block;
	    	margin-bottom: 10px;
	    	box-sizing: border-box;
    	}
      input[type="password"]{
    		color: #545454;
	    	height: 60px;
	    	width: 100%;
	    	padding: 5px 10px;
	    	font-size: 16px;
	    	display: block;
	    	margin-bottom: 10px;
	    	box-sizing: border-box;
    	}
    	input[type="submit"]{
	    	border: none;
	    	padding: 15px 30px;
	    	margin-bottom: 15px;
	    	background: black;
	    	color: white;
	    	float: right;
    	}
    	input[type="submit"]:hover{
	    	background: #3d3938;
	    	cursor: pointer;
    	}
    	a{
	    	color: #545454;
	    	display: block;
    	}
    	a:hover{
	    	text-decoration: none;
    	}
    </style>
  </head>
  <body>
   <h1 style="text-align:center; color:#333;">ゲーム「ドラ◯エ!!」</h1>
    <div style="background:black; padding:15px; position:relative;">
      <?php if(empty($_SESSION)){ ?>
        <h2 style="margin-top:60px;">GAME START ?</h2>
        <form method="post">
          <input type="submit" name="brave_start" value="▶勇者でゲームスタート">
          <input type="submit" name="wizard_start" value="▶魔法使いでゲームスタート">
        </form>
      <?php }else if(!empty($_SESSION['monster'])){ ?>
        <h2><?php echo $_SESSION['monster']->getName().'が現れた!!'; ?></h2>
        <div style="height: 150px;">
          <img src="<?php echo $_SESSION['monster']->getImg(); ?>" style="width:120px; height:auto; margin:40px auto 0 auto; display:block;">
        </div>
        <p style="font-size:14px; text-align:center;">モンスターのHP：<?php echo $_SESSION['monster']->getHp(); ?></p>
        <p>倒したモンスター数：<?php echo $_SESSION['knockDownCount']; ?></p>
        <?php if($_SESSION['playertype'] === 1){ ?>
          <p>勇者の残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
        <?php }else{ ?>
          <p>魔法使いの残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
        <?php } ?>
        <form method="post">
          <input type="submit" name="heal" value="▶回復する">
          <input type="submit" name="attack" value="▶攻撃する">
          <input type="submit" name="escape" value="▶逃げる">
          <input type="submit" name="start" value="▶ゲームリスタート">
        </form>
        <?php }else{ ?>
        <h2><?php echo $_SESSION['god']->getName().'が現れた!!'; ?></h2>
        <div style="height: 150px;">
          <img src="<?php echo $_SESSION['god']->getImg(); ?>" style="width:120px; height:auto; margin:40px auto 0 auto; display:block;">
        </div>
        <p style="font-size:14px; text-align:center;">選べ！！</p>
        <p>倒したモンスター数：<?php echo $_SESSION['knockDownCount']; ?></p>
        <?php if($_SESSION['playertype'] === 1){ ?>
          <p>勇者の残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
        <?php }else{ ?>
          <p>魔法使いの残りHP：<?php echo $_SESSION['human']->getHp(); ?></p>
        <?php } ?>
        <form method="post">
          <input type="submit" name="superheal" value="▶回復してもらう">
          <input type="submit" name="powerup" value="▶強くしてもらう">
          <input type="submit" name="buildup" value="▶丈夫にしてもらう">
          <input type="submit" name="start" value="▶ゲームリスタート">
        </form>
      <?php } ?>
      <div style="position:absolute; right:-350px; top:0; color:black; width: 300px;">
        <p><?php echo (!empty($_SESSION['history'])) ? $_SESSION['history'] : ''; ?></p>
      </div>
    </div>
    
  </body>
</html>
