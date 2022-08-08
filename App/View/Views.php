<?php

class Views
{
	public function render($filename)
	{
		$file = file_get_contents(__DIR__."/../../views/".$filename.".html");
		return $file;
	}

	public function page($name, $tags = [], $scripts = "")
	{
		$page = $this->render($name);
		$page = str_replace("{{menu}}", $this->render("menu"), $page);
		$page = str_replace("{{header}}", $this->render("header"), $page);
		$page = str_replace("{{scripts}}", $this->render("scripts").$scripts, $page);

		foreach ($tags as $key => $value) {
			$page = str_replace("{{".$key."}}", $value, $page);
		}

		$page = preg_replace("/({{)(.*?)(}})/", "", $page);
		return $page;
	}
}
