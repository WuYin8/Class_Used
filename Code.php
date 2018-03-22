<?php
namespace framework;
/*
1、该类对外公开的方法只有一个 outImage,只要调用这个方法，就可以将验证码显示到浏览器，其它的为这个类服务的方法我们搞成protected，供子类来继承和重写
2、有些变量在该类里面会被反复的使用到，我们将其搞成成员属性，将不用公开的成员属性设置为protected
*/
class Code
{	
	// 验证码属性
	protected $codeType; // 验证码类型
	protected $codeNum; // 验证码个数
	protected $code; // 验证码文本
	// 图片属性
	protected $image; // 图片资源
	protected $width; // 图片宽度
	protected $height; // 图片高度
	protected $imageType; // 图片类型

	function __construct($codeType = 2 , $codeNum = 6 , $width = 200 , $height = 70 , $imageType = 'png')
	{
		$this->codeType = $codeType;
		$this->codeNum = $codeNum;
		$this->width = $width;
		$this->height = $height;
		$this->imageType = $imageType;
		// 通过方法获取验证码
		$this->code = $this->getCode();
	}

	function __destruct()
	{
		imagedestroy($this->image);
	}

	function __get($name)
	{
		if ($name == 'code') {
			return $this->code;
		}
	}

	// 获取验证码文本
	function getCode()
	{
		switch ($this->codeType) {
			// 纯数字验证码
			case 0:
				$code = $this->getNumCode();
				break;
			// 纯字母验证码
			case 1:
				$code = $this->getAbcCode();
				break;
			// 字母数字混合验证码
			case 2:
				$code = $this->getMixCode();
				break;
			// 验证码类型错误
			default:
				exit('验证码类型错误');
				break;
		}
		return $code;
	}

	// 获取纯数字验证码
	protected function getNumCode()
	{
		$str1 = join('' , range(0, 9));
		$str = substr(str_shuffle($str1) , 0 ,$this->codeNum);
		return $str;
	}

	// 获取纯字母验证码
	protected function getAbcCode()
	{
		$str1 = join('' , range('a' , 'z')) . join('' , range('A' , 'Z'));
		$str = substr(str_shuffle($str1), 0 , $this->codeNum);
		return $str;
	}

	// 获取混合验证码
	protected function getMixCode()
	{
		$str1 = join('' , range('a' , 'z')) . join('' , range('A' , 'Z')) . join('' , range(0 , 9));
		$str = substr(str_shuffle($str1) , 0 , $this->codeNum);
		return $str;
	}

	// 生成图片
	function getImage()
	{
		// 生成图片资源
		$this->image = $this->createImg();
		// 填充背景颜色
		$this->backFill();
		// 添加验证码
		$this->addCode();
		// 添加干扰元素
		$this->addShit();
		// 输出图片
		$this->showImg();
	}

	// 生成图片资源
	protected function createImg()
	{
		return imagecreatetruecolor($this->width, $this->height);
	}

	// 颜色填充
	protected function backFill()
	{
		imagefill($this->image, 0, 0, $this->colorL());
	}

	// 调用浅色系
	protected function colorL()
	{
		return imagecolorallocate($this->image, 
				mt_rand(130 , 255),
				mt_rand(130 , 255),
				mt_rand(130 , 255));
	}
	// 调用深色系
	protected function colorH()
	{
		return imagecolorallocate($this->image, 
				mt_rand(0 , 120), 
				mt_rand(0 , 120), 
				mt_rand(0 , 120));
	}

	// 添加验证码
	protected function addCode()
	{
		for ($i=0; $i < $this->codeNum; $i++) { 
			$c = $this->code[$i];
			$sWidth = ceil($this->width / $this->codeNum);
			$x = mt_rand($sWidth * $i + 10 , $sWidth * ($i + 1) - 15);
			$y = mt_rand(0 , $this->height - 10);
			imagechar($this->image, 5, $x, $y, $c, $this->colorH());
		}
	}

	// 添加干扰元素
	protected function addShit()
	{
		// 添加干扰点
		for ($i=0; $i < 400; $i++) { 
			$x = mt_rand(0 , $this->width);
			$y = mt_rand(0 , $this->height);
			imagesetpixel($this->image, $x, $y, $this->colorH());
		}

		// 添加干扰线
		for ($i=0; $i < 5; $i++) { 
			imagearc($this->image, 
				mt_rand(0 , $this->width), 
				mt_rand(0 , $this->height), 
				mt_rand(0 , $this->width), 
				mt_rand(0 , $this->height), 
				mt_rand(30 , 120), 
				mt_rand(180 , 360), 
				$this->colorH());
		}
	}

	// 输出图片
	protected function showImg()
	{
		header('Content-Type:image/' . $this->imageType);
		$func = 'image' . $this->imageType;
		$func($this->image);

	}
}
// $code = new Code();
// echo $code->getImage();









