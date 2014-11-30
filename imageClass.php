<?php
/**
 * 图片处理类
 * @authors Andy Chen (bootoo@sina.cn)
 * @date    2014-03-12 21:16:12
 * @version $Id$
 */

class imageClass {
    
    function __construct(){
        $this->fontfile = 'addons\\image\\XHei.ttc';
    }
	
	/**
	 * 验证码图片生成方法
	 * @param int    $speck_num  画多少个点
	 * @param int    $line_num   画多少条线
	 * @param int    $font_num   填充文字数量
	 * @return img   $im  
	 */
	public function verification($speck_num,$line_num,$font_num){
        $_SESSION['ver_code'] = '';

		$im = imagecreatetruecolor(100, 30);    //画布
		$text = '23456789abcdefghijkmnpqrstuvwxyzABCDEFGHIJKMNPQRSTUVWXYZ';  //填充内容(已经去除10lo)
		// 随机背景颜色
		$red     = mt_rand(110,130);
		$green = mt_rand(110,130);
		$blue   = mt_rand(110,130);
		$back_color = imagecolorallocate($im, $red, $green, $blue); //背景颜色
		$border_color = imagecolorallocate($im, 0, 114, 198);       //边框颜色
		imagefilledrectangle($im,0,0,100,30,$back_color);           //画背景 
		imagerectangle($im, 0, 0, 100, 30, $border_color);          //边框

		// 随机生成点
		for ($i=0; $i < $speck_num; $i++) { 
			$red   = mt_rand(0,255);
			$green = mt_rand(0,255);
			$blue  = mt_rand(0,255);
			$x     = mt_rand(0,100);
			$y     = mt_rand(0,30);
			$speck_color = imagecolorallocate($im, $red, $green, $blue);    //点(speck:杂点，污点)颜色
			imagesetpixel($im, $x, $y, $speck_color);                       //画点
		}

		// 随机生成线
		for ($i=0; $i < $line_num; $i++) { 
			$red    = mt_rand(0,255);
			$green  = mt_rand(0,255);
			$blue   = mt_rand(0,255);
			$cx     = mt_rand(0,100);
			$cy     = mt_rand(0,30);
			$width  = mt_rand(0,90);
			$height = mt_rand(0,90);
			$start  = mt_rand(0,360);
			$end    = mt_rand(0,360);
			$line_color = imagecolorallocate($im, $red, $green, $blue);            //线条颜色
			imagearc($im, $cx, $cy, $width, $height, $start, $end, $line_color);   //画线
		}

		// 随机生成文字
		$fonts = '';
        for ($i=1; $i <= $font_num; $i++) { 
			$red   = mt_rand(0,255);
			$green = mt_rand(0,255);
			$blue  = mt_rand(0,255);
			$size  = mt_rand(20,26);
			$angle = mt_rand(0,26);
			$x     = 15*$i;
			$y     = mt_rand(25,30);
			$font  = '';
			$start = mt_rand(0,(strlen($text)-1));
			$font  = substr($text, $start,1);
			$font_color = imagecolorallocate($im, 0, 0, 0);   //字体颜色
			imagefttext($im, $size, $angle, $x, $y, $font_color, $this->fontfile, $font);   //填充文字
			$fonts .= $font;
        }
        $_SESSION['ver_code'] = $fonts;

		return $im;
	}

	/**
	 * 图片缩放方法
	 * @param str    $file      图片路径
	 * @param str    $newFile   要生成的新文件名和路径
	 * @param int    $width     要缩放的宽度
	 * @param int    $height    要缩放的高度
	 * @return str   $newFile   缩放后的图片路径
	 */
    public function thumb($file,$newFile,$width,$height){
    	$old_img = imagecreatefromjpeg($file);

    	// 得到图片的原宽高
		$old_width  = imagesx($old_img);
		$old_height = imagesy($old_img);

		// 原图比例
		$ratio = $old_width/$old_height;  

		// 宽够，高够
		if ($old_width >= $width and $old_height >= $height) { 
			if ($width > $height) {
				$new_width  = $width;
				$new_height = $width/$ratio;
			} elseif($width < $height) {
				$new_width  = $height*$ratio;
				$new_height = $height;
			}else{
				if ($ratio >= 1) {
					$new_width  = $height*$ratio;
					$new_height = $height;
				} else {
					$new_width  = $width;
					$new_height = $width/$ratio;
				}
			}
			
			if ($ratio >= 1) {
				$nx = -(($new_width/2) - ($width/2));
				$ny = 0;
			} else {
				$nx = ($width/2) - ($new_width/2);
				$ny = -(($new_height/2) - ($height/2));
			}
		}

		// 宽够，高不够
  		if ($old_width >= $width and $old_height < $height) { 
			$new_width  = $width;
			$new_height = $width/$ratio;
			$nx = 0;
			$ny = ($height/2) - ($new_height/2);
		}

		// 宽不够，高够
  		if ($old_width < $width and $old_height >= $height) { 
			$new_width  = $height*$ratio;
			$new_height = $height;
			$nx = ($width/2) - ($new_width/2);
			$ny = 0;
		}

		// 宽不够，高不够
  		if ($old_width < $width and $old_height < $height) { 
			$new_width  = $old_width;
			$new_height = $old_height;
			$nx = ($width/2) - ($new_width/2);
			$ny = ($height/2) - ($new_height/2);
		}

		//原图坐标
		$ox = 0;
		$oy = 0;

		//生成新图片
		$new_img    = imagecreatetruecolor($width,$height);
		$back_color = imagecolorallocate($new_img, 255, 255, 255);        //新图片背景颜色
		imagefilledrectangle($new_img,0,0,$width,$height,$back_color);    //新图片背景 

		//创建新的图像对象(缩略图)
		imagecopyresized($new_img,$old_img,$nx,$ny,$ox,$oy, $new_width,$new_height,$old_width,$old_height);

		// 生成图片
	    imagejpeg($new_img,$newFile);

	    // 销毁图片
	    imagedestroy($new_img);

		return $newFile;
    }

	/**
	 * 图片加水印方法
	 * @param arr       $info    原图信息
	 * @param str/arr   $wt_con  水印内容
	 * @param str       $seat    水印位置 1.up_l(上左)  2.up_r(上右)  3.do_l(下左)  4.do_r(下右)
	 * @param arr       $gap     边距 (width=>侧边距离，height=>底部距离/顶部距离)
	 * @param int       $alpha   透明度
	 */	
	public function waterMark($info,$wt_con,$seat,$gap,$alpha=''){
		$old_path = $info['path'];

		// 得到原图
		$old_img = imagecreatefromjpeg($old_path);
		$old_info = getimagesize($old_path);


		// 得到水印信息(区分图片或文字)
		if (is_array($wt_con)) {
			$font = imagettfbbox($wt_con['size'], 0, $this->fontfile, $wt_con['text']);
            $wt_info['0'] = $font[4] - $font[6];
            $wt_info['1'] = $font[1] - $font[7];
		} else {
		    $wt_img = imagecreatefrompng($wt_con);
			$wt_info = getimagesize($wt_con);
		}
		
        // 计算水印位置
        $water_seat = $this->wmGap($old_info,$wt_info,$seat,$gap);

		// 打水印(区分图片或文字)
		if (is_array($wt_con)) {
			$font_color = imagecolorallocate($old_img, 234, 234, 234);   
			// 下面的$water_seat['gap_h']+20 因 imagettfbbox() 坐标系统和其他的统一所致
			imagettftext($old_img, $wt_con['size'], 0, $water_seat['gap_w'], $water_seat['gap_h']+20, $font_color, $this->fontfile, $wt_con['text']);
		} else {
			if ($alpha == '') {
		        imagecopy($old_img, $wt_img, $water_seat['gap_w'], $water_seat['gap_h'], 0, 0, $wt_info['0'], $wt_info['1']);  //无背景，不可透明
			} else {
				imagecopymerge($old_img, $wt_img, $water_seat['gap_w'], $water_seat['gap_h'], 0, 0, $wt_info['0'], $wt_info['1'], $alpha);  //有背景，可透明
			}
			
		}
		
        // 生成加水印后的图片
        imagepng($old_img,$old_path);

        // 销毁图片
        imagedestroy($old_img);
        imagedestroy($wt_img);

	}	

	/**
	 * 计算水印位置
	 * @param arr    $old_info   原图信息
	 * @param arr    $wt_info    水印图信息
	 * @param str    $seat       位置
	 * @param int    $gap        边距
	 * @return arr   $arr        处理好的数组
	 */	
	public function wmGap($old_info,$wt_info,$seat,$gap){
        switch ($seat) {
        	case 'up_l':
        	    $arr['gap_w'] = $gap['0']; 
        	    $arr['gap_h'] = $gap['1']; 
        		break;
        	
        	case 'up_r':
        	    $width = $old_info['0'] - $wt_info['0'] - $gap['0'];
        	    $arr['gap_w'] = $width; 
        	    $arr['gap_h'] = $gap['1']; 
        		break;
        	
        	case 'do_l':
        	    $height = $old_info['1'] - $wt_info['1'] - $gap['1'];
        	    $arr['gap_w'] = $gap['0']; 
        	    $arr['gap_h'] = $height; 
        		break;
        	
        	case 'do_r':
        	    $width  = $old_info['0'] - $wt_info['0'] - $gap['0'];
        	    $height = $old_info['1'] - $wt_info['1'] - $gap['1'];
        	    $arr['gap_w'] = $width; 
        	    $arr['gap_h'] = $height; 
        		break;
        	
        	default:
        	    $arr['gap_w'] = $gap['0']; 
        	    $arr['gap_h'] = $gap['1']; 
        	    break;
        }
        return $arr;
	}


}
?>