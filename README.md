php image class
===========

## 验证码图片
```php
header("Cache-Control:max-age=1,s-maxage=1,no-cache,must-revalidate");
header("Content-type:image/png;charset=utf8");
require_once './imageClass.php';
$obj = new imageClass;
$im = $obj->verification(1000,10,4);

// 生成图片
imagepng($im);
// 销毁图片
imagedestroy($im);
```


## 图片打图片水印
```php
require_once './imageClass.php';
$img_path = 'your image path';
$wt_con   = './wt.png';
$obj      = new imageClass;
$obj->waterMark($img_path,$wt_con,'do_l',array('10','10'));
```
## 图片打文字水印
```php
require_once './imageClass.php';
$wt_con['size'] = '14';
$wt_con['text'] = '文字水印';
$img_path = 'your image path';
$obj      = new imageClass;
$obj->waterMark($img_path,$wt_con,'do_l',array('10','10'));
```

## 等比例缩放图片
```php
require_once './imageClass.php';
$width    = 100;
$height   = 50;
$img_path = 'your image path';
$newfile  = 'data/images/2013';
$obj      = new imageClass;
$obj->thumb($img_path,$newFile,$width,$height);
```

