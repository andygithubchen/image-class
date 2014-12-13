image-class
===========

实用如下：

header("Cache-Control:max-age=1,s-maxage=1,no-cache,must-revalidate");
header("Content-type:image/png;charset=utf8");
include 'XHei.TTC';
$obj = new imageClass;
$im = $obj->verification(1000,10,4);

// 生成图片
imagepng($im);
// 销毁图片
imagedestroy($im);
