<?php

namespace src;

class util
{
	public static $needParams = [];


	public static function getTemplates($dir, $base)
	{
		$nodes = [];
		$files = scandir($dir);
		foreach ($files as $file) {
			if ($file == '.' || $file == '..') {
				continue;
			}
			$path = $dir . '/' . $file;
			$path2 = str_replace($base . '/', '', $path);
			if (str_ends_with($path2, '.json')) {
				continue;
			}
			if ($file == '__config.php') {	//配置文件不是模板
				continue;
			}
			$node = [
				'id' => $path2,
				'text' => $file,
			];
			if (is_dir($path)) {    //子文件夹
				$node['type'] = 'folder';
				$childrens = self::getTemplates($path, $base);
				$node['children'] = $childrens;
			} else {    //文件
				$node['type'] = 'file';
			}
			$nodes[] = $node;
		}
		return $nodes;
	}

	public static function getFilePathFromHeader($response, $ch)
	{
		$header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
		$header = substr($response, 0, $header_size);
		$headers = explode("\r\n", $header);
		$path = '';
		foreach ($headers as $header) {
			if (strpos($header, 'filepath') !== false) {
				$path = substr($header, strpos($header, ':') + 1);
			}
		}
		return trim($path);
	}

	public static function setSavePath($path, $filename)
	{
		if ($path) {
			if (str_ends_with($path, '/') || str_ends_with($path, '\\')) {
				$path = substr($path, 0, strlen($path) - 1);
			}
			header('filepath:' . $path . '\\' . $filename);
		}
	}

	/**
	 * @param $name string 参数名
	 * @param $default string 模板中的默认值
	 * @return mixed|null
	 */
	public static function param($name, $default = null)
	{
		$paramValue = $_GET[$name] ? $_GET[$name] : $default;
		//记录需要的参数
//		self::$needParams[$name] = $paramValue;
		//返回参数
		return $paramValue;
	}

	public static function initParam($param)
	{
		//遍历GET参数
		foreach ($_GET as $key => $value) {
			$param[$key] = $value;
		}
		return $param;
	}

	/**
	 * 如果是获取参数的请求，直接结束响应返回参数
	 * @param $template
	 * @return void
	 */
	public static function getParamCache($template)
	{
		$cacheParams = [];
		$paramFile = $template . '.json';
		//判断$paramFile文件是否存在
		if (file_exists($paramFile)) {
			$content = file_get_contents($paramFile);
			$cacheParams = json_decode($content, true);
		}
		if ($cacheParams) {
			foreach ($cacheParams as $key => $value) {
				self::$needParams[$key] = $value;
			}
		}
		return $cacheParams;
	}

	public static function response($data)
	{
		header('Content-Type: application/json');
		echo json_encode($data);
		exit();
	}

	public static function saveFile($filePath, $content)
	{
		//获取路径的文件夹部分
		$dir = substr($filePath, 0, strrpos($filePath, '\\'));
		//如果文件夹不存在，则创建
		if (!file_exists($dir)) {
			mkdir($dir, 0777, true);
		}
		return file_put_contents($filePath, $content);
	}
}